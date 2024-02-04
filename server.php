<?php

# Model
class Model
{
    public $database_name;
    public $servername;
    public $username;
    public $password;
    public $database_connection;
    public function __construct($database_name, $servername, $username, $password)
    {
        $this->database_name = $database_name;
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
    }

    # Get and Setters
    public function get_database_name(): string
    {
        return $this->database_name;
    }

    public function set_database_name(string $new_database_name)
    {
        $this->database_name = $new_database_name;
    }

    public function get_server_name(): string
    {
        return $this->servername;
    }

    public function set_server_name(string $new_server_name)
    {
        $this->servername = $new_server_name;
    }

    public function get_username(): string
    {
        return $this->username;
    }

    public function set_username(string $new_username)
    {
        $this->username = $new_username;
    }

    public function get_password(): string
    {
        return $this->password;
    }

    public function set_password(string $new_password)
    {
        $this->password = $new_password;
    }

    # Database operations
    protected function open_database_connection()
    {
        $this->database_connection = new mysqli($this->servername, $this->username, $this->password, $this->database_name);

        if (!$this->database_connection) {
            die("Connection Failed: " . $this->database_connection->connect_error . "<br>");
        } else {
            return $this->database_connection;
        }
    }

    protected function close_database_connection()
    {
        try {
            $this->database_connection->close();
        } catch (Exception $closeConnectionError) {
            echo "Error While Closing Connection: " . $closeConnectionError->getMessage() . "<br>";
        }
    }
}

# Controller

class DatabaseMethods extends Model
{

    public function __construct($database_name, $servername, $username, $password)
    {
        $this->database_name = $database_name;
        $this->username = $username;
        $this->servername = $servername;
        $this->password = $password;
    }

    public function register_user(string $username, string $user_email, int $user_cellphone): bool
    {
        try {
            $database_connection = $this->open_database_connection();

            try {

                # Inserting to the database
                $sqlQuery = "INSERT INTO users (name, email, phone_number)
                VALUES ('$username', '$user_email', '$user_cellphone')";

                if ($database_connection->query($sqlQuery) === true) {
                    echo json_encode("Successfully registraded " . $username);
                } else {
                    echo "Error While Registering: " . $sqlQuery . "<br>" . $database_connection->error;

                    return false;
                }

                $this->close_database_connection();

                return true;
            } catch (mysqli_sql_exception $mysqliSqlError) {
                echo "Couldn't Register " . $username . ", Because: " . $mysqliSqlError->getMessage() . "<br>" . "And -> " . $mysqliSqlError->getTraceAsString();

                $this->close_database_connection();

                return false;
            }
        } catch (Exception $databaseConnectionError) {
            echo "Couldn't Open a database connection: " . $databaseConnectionError->getMessage() . "<br>";

            $this->close_database_connection();

            return false;
        }
    }

    public function selects_all_users()
    {
        try {
            $database_connection = $this->open_database_connection();

            try {

                $sqlQuery = "SELECT * FROM users";
                $queryResult = $database_connection->query($sqlQuery);

                if ($queryResult->num_rows > 0) {
                    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Number</th><th>Action</th></tr>";

                    while ($row = $queryResult->fetch_assoc()) {
                        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["email"] . "</td><td>" . $row["phone_number"] . '</td><td> <button class="table-button" id="delete" onclick="delete_user(' . $row["id"] . ')" >delete</button> <br> <button class="table-button" id="edit" onclick="edit_user(' . $row["id"] . ')" >edit</button>' . "</td></tr>";
                    }

                    echo "</table>";
                } else {
                    echo "No Users Registrated !";
                }

                $this->close_database_connection();
            } catch (mysqli_sql_exception $mysqliSqlError) {
                echo "Couldn't Complete the request, Because: " . $mysqliSqlError->getMessage() . "<br>" . "And -> " . $mysqliSqlError->getTraceAsString();

                $this->close_database_connection();
            }
        } catch (Exception $databaseConnectionError) {
            echo "Couldn't Open a database connection: " . $databaseConnectionError->getMessage() . "<br>";

            $this->close_database_connection();
        }
    }

    public function delete_user(int $id)
    {
        try {
            $database_connection = $this->open_database_connection();

            try {

                $sqlQuery = "DELETE FROM users WHERE id=$id";

                if ($database_connection->query($sqlQuery) === true) {
                    echo json_encode("Successfully deleted record");
                } else {
                    echo json_encode("Error While Deleting Record: " . $sqlQuery . "<br>" . $database_connection->error);
                }

                $this->close_database_connection();
            } catch (mysqli_sql_exception $mysqliSqlError) {
                echo "Couldn't Complete the request, Because: " . $mysqliSqlError->getMessage() . "<br>" . "And -> " . $mysqliSqlError->getTraceAsString();

                $this->close_database_connection();
            }
        } catch (Exception $databaseConnectionError) {
            echo "Couldn't Open a database connection: " . $databaseConnectionError->getMessage() . "<br>";

            $this->close_database_connection();
        }
    }

    public function retrieve_user_data(int $user_ID)
    {
        try {
            $database_connection = $this->open_database_connection();

            try {

                $sqlQuery = "SELECT name, email, phone_number FROM users WHERE id=$user_ID";
                $queryResult = $database_connection->query($sqlQuery);

                if ($queryResult->num_rows > 0) {

                    while ($row = $queryResult->fetch_assoc()) {
                        $user_data = array("name" => $row['name'], "email" => $row['email'], "phone_number" => $row['phone_number']);
                    }

                    # returns a JSON representation of `$user_data` to the frontend
                    echo json_encode($user_data);
                } else {
                    echo "No User Was Found !";
                }

                $this->close_database_connection();
            } catch (mysqli_sql_exception $mysqliSqlError) {
                echo json_encode("Couldn't Complete the request, Because: " . $mysqliSqlError->getMessage() . "<br>" . "And -> " . $mysqliSqlError->getTraceAsString());

                $this->close_database_connection();
            }
        } catch (Exception $databaseConnectionError) {
            echo json_encode("Couldn't Open a database connection: " . $databaseConnectionError->getMessage() . "<br>");

            $this->close_database_connection();
        }
    }

    public function edit_user(int $id, $data)
    {
        try {
            $database_connection = $this->open_database_connection();

            try {

                $newName = $data['newName'];
                $newEmail = $data['newEmail'];
                $newPhoneNumber = $data['newPhone'];

                $sqlQuery = "UPDATE users SET name='$newName', email='$newEmail', phone_number=$newPhoneNumber WHERE id=$id";

                if ($database_connection->query($sqlQuery) === true) {
                    echo json_encode("Successfully updated registry");
                } else {
                    echo json_encode("Error While Updating Record: " . $sqlQuery . "<br>" . $database_connection->error);
                }
            } catch (mysqli_sql_exception $mysqliSqlError) {
                echo "Couldn't complete request because: " . $mysqliSqlError->getMessage() . "<br>" . "And -> " . $mysqliSqlError->getTraceAsString();

                $this->close_database_connection();
            }
        } catch (Exception $datababaseConnectionError) {
            echo "Couldn't open a database connection: " . $datababaseConnectionError->getMessage() . "<br>";

            $this->close_database_connection();
        }
    }
}

$Operations = new DatabaseMethods("phpmysqltest", "localhost", "root", "");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $request_body = file_get_contents("php://input");

    $request_body_data = json_decode($request_body, true);

    if (isset($request_body_data['name']) && isset($request_body_data['email']) && isset($request_body_data['phone_number'])) {

        $username = $request_body_data['name'];
        $user_email = $request_body_data['email'];
        $phonenumber = $request_body_data['phone_number'];

        $Operations->register_user($username, $user_email, $phonenumber);
    } else {
        echo json_encode("You must inform the new user informations");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === "GET") {

    $Operations->selects_all_users();
} elseif ($_SERVER['REQUEST_METHOD'] === "DELETE") {

    $request_body = file_get_contents("php://input");

    $request_body_data = json_decode($request_body, true);

    if (isset($request_body_data['id'])) {
        $id = (int) $request_body_data['id'];

        $Operations->delete_user($id);
    } else {
        http_response_code(400);
        echo json_encode("Please inform the ID");
    }
} else if ($_SERVER['REQUEST_METHOD'] === "PUT") {

    $request_body = file_get_contents("php://input");

    $request_body_data = json_decode($request_body, true);

    if (isset($request_body_data['id'])) {
        $user_ID = (int) $request_body_data['id'];

        try {

            $Operations->retrieve_user_data($user_ID);

            if (isset($request_body_data['edit'])) {
                $isEdit = (bool) $request_body_data['edit'];

                if ($isEdit === true) {
                    try {

                        $newData = array("newName" => $request_body_data['newName'], "newEmail" => $request_body_data['newEmail'], "newPhone" => $request_body_data['newPhone']);

                        $Operations->edit_user($user_ID, $newData);
                    } catch (Exception $updateRegistryError) {
                        http_response_code(500);
                        echo json_encode("Error while updating registry: " . $updateRegistryError->getMessage());
                    }
                }
            } else {
                return;
            }
        } catch (Exception $requestError) {
            echo json_encode("Unnable to retrieve user data: " . $requestError->getMessage());
        }
    } else {
        http_response_code(400);
        echo json_encode("Error Updating registry");
    }
}
