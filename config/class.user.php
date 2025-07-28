<?php

error_reporting(0);

require_once 'db.php';

class USER
{
	private $conn;

	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}

	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}

	public function lastID()
	{
		$stmt = $this->conn->lastInsertId();
		return $stmt;
	}

	public function register($uname, $fname, $contact, $email, $upass, $user_type_id)
	{
    try 
    {
        $password = password_hash($upass, PASSWORD_DEFAULT);
        $uniqueId = uniqid('UID_');

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO users (user_name, f_name, contact, user_email, user_pass, account_status, uniqid)
                                  VALUES (:uname, :fname, :contact, :email, :upass, 'active', :uniqid)");

        $stmt->bindParam(':uname', $uname);
        $stmt->bindParam(':fname', $fname);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':upass', $password);
        $stmt->bindParam(':uniqid', $uniqueId);

        $stmt->execute();
        $userId = $this->conn->lastInsertId();

        // Insert selected user type
        if ($user_type_id && $userId) {
            $mapStmt = $this->conn->prepare("INSERT INTO user_type_map (user_id, type_id) VALUES (:uid, :tid)");
            $mapStmt->bindParam(':uid', $userId);
            $mapStmt->bindParam(':tid', $user_type_id);
            $mapStmt->execute();
        }

        // Insert default 'user' type if not already selected
        $typeStmt = $this->conn->prepare("SELECT id FROM user_types WHERE type_name = 'user' LIMIT 1");
        $typeStmt->execute();
        $defaultTypeId = $typeStmt->fetchColumn();

        if ($defaultTypeId && $userId && $defaultTypeId != $user_type_id) {
            $mapStmt2 = $this->conn->prepare("INSERT INTO user_type_map (user_id, type_id) VALUES (:uid, :tid)");
            $mapStmt2->bindParam(':uid', $userId);
            $mapStmt2->bindParam(':tid', $defaultTypeId);
            $mapStmt2->execute();
        }

        return $stmt;
    } 
    catch (PDOException $ex) 
    {
        echo $ex->getMessage();
    }
	}


	public function login($email, $upass)
	{
    try {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_email = :email_id");
        $stmt->execute(array('email_id' => $email));
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1) {
            if ($userRow['account_status'] == 'active') {
                if (password_verify($upass, $userRow['user_pass'])) {
                    $_SESSION['userSession'] = $userRow['id'];
                    $_SESSION['user_email'] = $userRow['user_email'];
                    $_SESSION['user_name'] = $userRow['user_name'];
                    
                    // Update is_online status
                    $updateStmt = $this->conn->prepare("UPDATE users SET is_online = 1 WHERE id = :user_id");
                    $updateStmt->bindParam(':user_id', $userRow['id']);
                    $updateStmt->execute();
                    
                    return true;
                } else {
                    return 'invalid';
                }
            } else {
                return 'inactive';
            }
        } else {
            return 'not_found';
        }
    } catch (PDOException $ex) {
        error_log($ex->getMessage());
        return false;
    }
	}


	public function is_logged_in()
	{
    	return isset($_SESSION['userSession']);
	}

	// public function is_logged_in()
	// {
	// 	if($_SESSION['userSession'])
	// 	{
	// 		return true;
	// 	}
	// }

	public function redirect($url)
	{
		header("Location: $url");
		exit;
	}

	public function logout()
	{
    if (isset($_SESSION['userSession'])) {
        $userId = $_SESSION['userSession'];
        $stmt = $this->conn->prepare("UPDATE users SET is_online = 0 WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    session_destroy();
    $_SESSION['userSession'] = false;
	}

	public function getActiveProjects()
{
	try {
		$stmt = $this->conn->prepare("SELECT * FROM projects WHERE project_status = 'active'");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $ex) {
		echo $ex->getMessage();
		return [];
	}
}



	public function sendMail($email, $message, $subject)
	{
		require_once 'mailer/PHPMailer.php';
		require_once 'mailer/SMTP.php';

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		//$mail->SMTPDebug = 3;
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'sheehan9010@gmail.com';
		$mail->Password = 'bxconufqnfuxontz';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->setFrom('sheehan9010@gmail.com','GrowNet');
		$mail->addAddress($email);
		//$mail->addReplyTo('sheehan9010@gmail.com','GrowNet');
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $message;

		if(!$mail->send())
		{
			$_SESSION['mailError'] = $mail->ErrorInfo;
			return false;
		}

		else
		{
			return true;
		}

	}

public function investInProject($userId, $projectId, $shares)
{
    try {
        // 1. Check if project exists and is active, and get price per share and founder id
        $projectStmt = $this->conn->prepare("SELECT shares, price_per_share, owner_id FROM projects WHERE id = :pid AND project_status = 'active'");
        $projectStmt->execute([':pid' => $projectId]);
        $project = $projectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            return ['success' => false, 'message' => 'Project not found or not active.'];
        }

        // 2. Calculate shares left
        $orderStmt = $this->conn->prepare("SELECT SUM(shares_bought) AS total_sold FROM share_orders WHERE project_id = :pid");
        $orderStmt->execute([':pid' => $projectId]);
        $sold = (int)($orderStmt->fetchColumn() ?? 0);
        $left = (int)$project['shares'] - $sold;

        if ($shares < 1 || $shares > $left) {
            return ['success' => false, 'message' => 'Invalid number of shares requested.'];
        }

        // 3. Calculate total investment amount
        $totalAmount = $shares * (float)$project['price_per_share'];

        // 4. Fetch user's current balance
        $balanceStmt = $this->conn->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN type IN ('deposit','investment_return','admin_adjustment','profit','donation','loan_repayment') THEN amount ELSE 0 END) -
                SUM(CASE WHEN type IN ('withdraw','loan_disbursement','penalty_charge','investment') THEN amount ELSE 0 END), 0) AS current_balance
            FROM balance_transactions WHERE user_id = :uid
        ");
        $balanceStmt->execute([':uid' => $userId]);
        $balanceRow = $balanceStmt->fetch(PDO::FETCH_ASSOC);
        $current_balance = (float)($balanceRow['current_balance'] ?? 0);

        if ($current_balance < $totalAmount) {
            return ['success' => false, 'message' => 'Insufficient balance to invest.'];
        }

        // 5. Fetch uniqid for sender (investor) and receiver (founder)
        $stmtInvestor = $this->conn->prepare("SELECT uniqid FROM users WHERE id = :id");
        $stmtInvestor->execute([':id' => $userId]);
        $investorUniq = $stmtInvestor->fetchColumn();

        $founderId = $project['owner_id'];
        $stmtFounder = $this->conn->prepare("SELECT uniqid FROM users WHERE id = :id");
        $stmtFounder->execute([':id' => $founderId]);
        $founderUniq = $stmtFounder->fetchColumn();

        // 6. Insert share order
        $insertStmt = $this->conn->prepare("INSERT INTO share_orders (project_id, user_id, shares_bought) VALUES (:pid, :uid, :shares)");
        $insertStmt->execute([
            ':pid' => $projectId,
            ':uid' => $userId,
            ':shares' => $shares
        ]);

        // 7. Insert transactions for both investor and founder
        $txnUniq = uniqid('txn_');
        $descInvestor = "Investment in project ID $projectId ($shares shares)";
        $descFounder = "Investment received for project ID $projectId ($shares shares) from user $investorUniq";

        // Investor transaction (negative amount)
        $investorStmt = $this->conn->prepare(
            "INSERT INTO balance_transactions 
                (user_id, type, amount, description, uniqid, sender_id, receiver_id) 
             VALUES 
                (:uid, 'investment', :amt, :desc, :uniqid, :sender_uniqid, :receiver_uniqid)"
        );
        $investorStmt->execute([
            ':uid' => $userId,
            ':amt' => -$totalAmount,
            ':desc' => $descInvestor,
            ':uniqid' => $txnUniq,
            ':sender_uniqid' => $investorUniq,
            ':receiver_uniqid' => $founderUniq
        ]);

        // Founder transaction (positive amount)
        $founderStmt = $this->conn->prepare(
            "INSERT INTO balance_transactions 
                (user_id, type, amount, description, uniqid, sender_id, receiver_id) 
             VALUES 
                (:uid, 'investment', :amt, :desc, :uniqid, :sender_uniqid, :receiver_uniqid)"
        );
        $founderStmt->execute([
            ':uid' => $founderId,
            ':amt' => $totalAmount,
            ':desc' => $descFounder,
            ':uniqid' => $txnUniq,
            ':sender_uniqid' => $investorUniq,
            ':receiver_uniqid' => $founderUniq
        ]);

        return ['success' => true, 'message' => 'Investment successful.'];
    } catch (PDOException $ex) {
        return ['success' => false, 'message' => $ex->getMessage()];
    }
}

public function addProjectReview($userId, $projectId, $rating, $review)
{
    try {
        // Check if project is closed
        $projectStmt = $this->conn->prepare("SELECT project_status FROM projects WHERE id = :pid");
        $projectStmt->execute([':pid' => $projectId]);
        $project = $projectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$project || strtolower($project['project_status']) !== 'closed') {
            return ['success' => false, 'message' => 'You can only review closed projects.'];
        }

        // Check if user has already reviewed this project
        $checkStmt = $this->conn->prepare("SELECT id FROM project_reviews WHERE user_id = :uid AND project_id = :pid");
        $checkStmt->execute([':uid' => $userId, ':pid' => $projectId]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'You have already reviewed this project.'];
        }

        // Insert review
        $insertStmt = $this->conn->prepare("INSERT INTO project_reviews (user_id, project_id, rating, review, created_at) VALUES (:uid, :pid, :rating, :review, NOW())");
        $insertStmt->execute([
            ':uid' => $userId,
            ':pid' => $projectId,
            ':rating' => $rating,
            ':review' => $review
        ]);

        return ['success' => true, 'message' => 'Review submitted successfully.'];
    } catch (PDOException $ex) {
        return ['success' => false, 'message' => $ex->getMessage()];
    }
}

public function getProjectReviews($projectId)
{
    try {
        $stmt = $this->conn->prepare("
            SELECT pr.*, u.user_name, u.f_name
            FROM project_reviews pr
            INNER JOIN users u ON pr.user_id = u.id
            WHERE pr.project_id = :pid
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute([':pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        return [];
    }
}

public function getImpactMetrics()
{
    try {
        // Total Active Projects
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_active_projects FROM projects WHERE project_status = 'active'");
        $stmt->execute();
        $totalActiveProjects = $stmt->fetchColumn() ?: 0;

        // Total Investment Received
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(amount),0) FROM balance_transactions WHERE type = 'deposit'");
        $stmt->execute();
        $totalInvestment = $stmt->fetchColumn() ?: 0;

        // Total Unique Investors
        $stmt = $this->conn->prepare("SELECT COUNT(DISTINCT user_id) FROM share_orders");
        $stmt->execute();
        $totalInvestors = $stmt->fetchColumn() ?: 0;

        // Total Loans Approved
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM loans WHERE status = 'approved'");
        $stmt->execute();
        $totalLoansApproved = $stmt->fetchColumn() ?: 0;

        return [
            'total_active_projects' => $totalActiveProjects,
            'total_investment' => $totalInvestment,
            'total_investors' => $totalInvestors,
            'total_loans_approved' => $totalLoansApproved
        ];
    } catch (PDOException $ex) {
        // optionally log error
        return [
            'total_active_projects' => 0,
            'total_investment' => 0,
            'total_investors' => 0,
            'total_loans_approved' => 0
        ];
    }
}

public function getConnection()
{
    return $this->conn;
}
}

?>

