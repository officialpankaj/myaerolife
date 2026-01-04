<?php
class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            // Set PHP timezone
            date_default_timezone_set('Asia/Kolkata');

            $this->connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

            if (mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");
            }
            if ($this->connection->connect_error) {
                die("Connection failed: " . $this->connection->connect_error);
            }

            // Set MySQL timezone for this connection
            $this->connection->query("SET time_zone = '+05:30';");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function select($query = "")
    {
        try {
            $result = $this->connection->query($query);
            if (isset($result->num_rows) && $result->num_rows > 0) {
                return $result;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }

    protected function update($query = "")
    {
        try {
            if ($this->connection->query($query) === true) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
