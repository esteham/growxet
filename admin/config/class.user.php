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

	public function register($uname, $fname, $contact, $email, $upass)
	{
    	try 
    	{
        		$password = password_hash($upass, PASSWORD_DEFAULT);
        		$uniqueId = uniqid('UID_'); // Generate something like UID_665fe0c14ba2e

        		$stmt = $this->conn->prepare("INSERT INTO users (user_name, f_name, contact, user_email, user_pass, account_status, uniqid)
                                      VALUES (:uname, :fname, :contact, :email, :upass, 'active', :uniqid)");

        		$stmt->bindParam(':uname', $uname);
        		$stmt->bindParam(':fname', $fname);
        		$stmt->bindParam(':contact', $contact);
        		$stmt->bindParam(':email', $email);
        		$stmt->bindParam(':upass', $password);
        		$stmt->bindParam(':uniqid', $uniqueId);

        		$stmt->execute();
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
	

public function adminLogin($email, $upass)
{
    try {
        $stmt = $this->conn->prepare(
            "SELECT u.* 
             FROM users u
             JOIN user_type_map utm ON u.id = utm.user_id
             WHERE u.user_email = :email_id AND utm.type_id = 1"
        );
        $stmt->execute(['email_id' => $email]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            if ($userRow['account_status'] == 'active') {
                if (password_verify($upass, $userRow['user_pass'])) {
                    $_SESSION['userSession'] = $userRow['id'];
                    $_SESSION['user_type'] = 'admin';
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

	public function adminLogout()
	{
    if (isset($_SESSION['userSession']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
        $userId = $_SESSION['userSession'];
        // Set is_online to 0 for the admin user
        $stmt = $this->conn->prepare("UPDATE users SET is_online = 0 WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    // Unset only admin-related session variables
	unset($_SESSION['userSession']);
	unset($_SESSION['user_type']);
	unset($_SESSION['user_email']);
	unset($_SESSION['user_name']);
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

function getUnseenProjectCount() {
    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM projects WHERE is_seen = 0 AND project_status = 'pending'");
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getProjectNotifications() {
    $stmt = $this->conn->prepare("SELECT * FROM projects WHERE project_status = 'pending' ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output = '';
    foreach ($projects as $project) {
        $statusClass = $project['is_seen'] ? '' : 'font-weight-bold';
        $output .= '<a class="dropdown-item '.$statusClass.'" href="index.php?page=pendingProjects&mark_seen='.$project['id'].'">
                      New Project: '.$project['project_name'].'
                      <small class="text-muted">'.$this->time_elapsed_string($project['created_at']).'</small>
                   </a>';
        if($project !== end($projects)) {
            $output .= '<div class="dropdown-divider"></div>';
        }
    }
    
    return $output ?: '<span class="dropdown-item text-muted">No new notifications</span>';
}

public function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    $result = array();
    foreach ($string as $k => $v) {
        if ($k == 'w' && $w) {
            $result[] = $w . ' ' . $v . ($w > 1 ? 's' : '');
        } elseif ($k != 'w' && $diff->$k) {
            $result[] = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        }
    }
    if (!$full) $result = array_slice($result, 0, 1);
    return $result ? implode(', ', $result) . ' ago' : 'just now';
}

function getProjectsByStatus($status) {
    $stmt = $this->conn->prepare("SELECT * FROM projects WHERE project_status = ? ORDER BY created_at DESC");
    $stmt->execute([$status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById($id) {
    $stmt = $this->conn->prepare("SELECT * FROM category WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserById($id) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function markProjectAsSeen($projectId) {
    $stmt = $this->conn->prepare("UPDATE projects SET is_seen = 1 WHERE id = ?");
    $stmt->execute([$projectId]);
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

public function approveProject($projectId) {
    $stmt = $this->conn->prepare("UPDATE projects SET project_status = 'active' WHERE id = ?");
    return $stmt->execute([$projectId]);
}

public function rejectProject($projectId, $reason = '') {
    $stmt = $this->conn->prepare("UPDATE projects SET project_status = 'declined', rejection_reason = ? WHERE id = ?");
    return $stmt->execute([$reason, $projectId]);
}

public function recordTransaction($userId, $type, $amount, $description = '')
{
    // Record in transactions table
    $stmt = $this->conn->prepare("
        INSERT INTO transactions (user_id, type, amount, description)
        VALUES (:uid, :type, :amount, :desc)
    ");
    $stmt->execute([
        ':uid' => $userId,
        ':type' => $type,
        ':amount' => $amount,
        ':desc' => $description
    ]);

    // Adjust user balance
    $operator = in_array($type, ['deposit', 'investment_return', 'admin_adjustment']) ? '+' : '-';

    $update = $this->conn->prepare("
        INSERT INTO user_balance (user_id, balance)
        VALUES (:uid, :amount)
        ON DUPLICATE KEY UPDATE balance = balance $operator :amount
    ");
    $update->execute([
        ':uid' => $userId,
        ':amount' => $amount
    ]);
}


public function getUserBalance($userId)
{
    $stmt = $this->conn->prepare("SELECT balance FROM user_balance WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() ?: 0; // Return 0 if no balance found

}

public function getUserTypes($userId)
{
    $stmt = $this->conn->prepare(
        "SELECT ut.type_name 
         FROM user_type_map utm
         JOIN user_types ut ON utm.type_id = ut.id
         WHERE utm.user_id = :user_id"
    );
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


public function beginTransaction() {
    return $this->conn->beginTransaction();
}

public function commit() {
    return $this->conn->commit();
}

public function rollBack() {
    return $this->conn->rollBack();
}


}


