<?php

/**
 * CHttpDbSession represent database session storage engine.
 *
 * @author kavolorn
 */
class CHttpDbSession extends CHttpSession
{
    /**
     * @var string the name of the table storing sessions
     */
    public $sessionTable = '__sessions';

    /**
     * @var string the ID of the {@link CDbConnection} application component. Defaults to 'db'.
     * The database must have the tables as declared in "framework/web/auth/schema.sql".
     * TODO: Добавить схему таблицы
     */
    public $connectionID = 'db';

    /**
     * @var CDbConnection the database connection. By default, this is initialized
     * automatically as the application component whose ID is indicated as {@link connectionID}.
     */
    public $db;

    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        $this->getDbConnection()->setActive(true);

        parent::init();
    }

    /**
     * Destructor.
     * Disconnect the db connection.
     */
    public function __destruct()
    {
        if ($this->db !== null) {
            $this->db->setActive(false);
        }
    }

    /**
     * @return CDbConnection the DB connection instance
     * @throws CException if {@link connectionID} does not point to a valid application component.
     */
    protected function getDbConnection()
    {
        if ($this->db !== null)
            return $this->db;
        else if (($this->db = Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection)
            return $this->db;
        else
            throw new CException(Yii::t('yii', 'СHttpDbSession.connectionID "{id}" is invalid. Please make sure it refers to the ID of a CDbConnection application component.',
                array('{id}' => $this->connectionID)));
    }

    /**
     * Indicates using custom session storage.
     * @return boolean whether to use custom storage
     */
    public function getUseCustomStorage()
    {
        return true;
    }

    /**
     * Session open handler.
     * @param string session save path
     * @param string session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Session close handler.
     * @return boolean whether session is closed successfully
     */
    public function closeSession()
    {
        return true;
    }

    /**
     * Session read handler.
     * @param string session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        $query = "SELECT `data`
                        FROM `{$this->sessionTable}`
                        WHERE `id` = :id";

        $command = $this->db->createCommand($query);
        $command->bindValue(':id', $id);

        return $command->queryScalar();
    }

    /**
     * Session write handler.
     * @param string session ID
     * @param string session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id, $data)
    {
        $query = "REPLACE INTO `{$this->sessionTable}`
                         VALUES (:id, UNIX_TIMESTAMP(), :data, 'PHPSESSID')";

        $command = $this->db->createCommand($query);
        $command->bindValue(':id', $id);
        $command->bindValue(':data', $data);

        $command->execute();

        return true;
    }

    /**
     * Session destroy handler.
     * @param string session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        $query = "DELETE
                         FROM `{$this->sessionTable}`
                         WHERE `id` = :id";

        $command = $this->db->createCommand($query);
        $command->bindValue(':id', $id);

        $command->execute();

        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * @param integer the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        $query = "DELETE
                         FROM `{$this->sessionTable}`
                         WHERE UNIX_TIMESTAMP() - `timestamp` > {$this->timeout}";

        $command = $this->db->createCommand($query);

        $command->execute();

        return true;
    }

    /**
     * Returns number of guests
     * @return int number of guests
     */
    public function getGuestsCount()
    {
        $query = "SELECT COUNT(*)
                         FROM `{$this->sessionTable}`
                         WHERE `data` = ''";

        $command = $this->db->createCommand($query);

        return $command->queryScalar();
    }

    /**
     * Returns number of authorised users
     * @return int number of authorised users
     */
    public function getUsersCount()
    {
        $query = "SELECT COUNT(*)
                         FROM `{$this->sessionTable}`
                         WHERE `data` <> ''";

        $command = $this->db->createCommand($query);

        return $command->queryScalar();
    }
}

?>
