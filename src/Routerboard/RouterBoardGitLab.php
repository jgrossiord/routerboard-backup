<?php

namespace Src\RouterBoard;

use Exception;
use Src\Adapters\RouterBoardDBAdapter;
use Src\Logger\OutputLogger;

class RouterBoardGitLab extends AbstractRouterBoard implements IRouterBoardBackup
{

    /** @var GitLabAPI $gitlab */
    private $gitlab;
    /** @var RouterBoardDBAdapter $dbConnect */
    private $dbConnect;
    /** @var SSHConnector $ssh */
    private $ssh;

    private $rootDir;
    private $filename;
    private $folder;

    /**
     * RouterBoardGitLab constructor.
     * @param array $config
     * @param OutputLogger $logger
     * @throws Exception
     */
    public function __construct(array $config, OutputLogger $logger)
    {
        parent::__construct($config, $logger);
        $this->gitlab = new GitLabAPI($this->config, $this->logger);
        if (!empty($this->config['gitlab']['group-name'])) {
            $this->checkExistGroup($this->config['gitlab']['group-name']);
        }
        $this->checkExistProject($this->config['gitlab']['project-name']);
        if ($this->config['gitlab']['debug'] == 1) {
            $this->logger->log("Project ID " . $this->gitlab->getProjectID(), $this->logger->setDebug());
            $this->logger->log("Group ID " . $this->gitlab->getGroupID(), $this->logger->setDebug());
        }
        $dbClass = $this->config['database']['data-adapter'];
        $this->dbConnect = new $dbClass($this->config, $this->logger);
		$this->ssh = new SSHConnector($this->config, $this->logger);
		$this->rootDir = $this->config['routerboard']['backupuser'];
		$this->filename = $this->rootDir;
		$this->folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->rootDir . DIRECTORY_SEPARATOR;
	}

    /**
     * @see \Src\RouterBoard\IRouterBoardBackup::backupAllRouterBoards()
     */
    public function backupAllRouterBoards()
    {
        if ($result = $this->dbConnect->getIP()) {
            foreach ($result as $data) {
                // get backup file from routerboard
                if (is_null($data['port']))
                    $data['port'] = $this->config['routerboard']['ssh-port'];
                if ($this->ssh->getBackupFile($data['addr'], $data['port'], $this->rootDir, $this->folder, $data['identity'])) {
                    // push both to the repository
                    $this->doGitLabPush($data['addr'], $data['identity'], 'backup', 'base64');
                    $this->doGitLabPush($data['addr'], $data['identity'], 'rsc', 'text');
                    $this->logger->log("Backup of the router " . $data['addr'] . " has been sucessfully.");
                    $this->dbConnect->updateBackupTime($data['addr']);
                }
            }
            return;
        }
        $this->logger->log('Get IP addresses from the database failed! Backup is not available. Try later.', $this->logger->setError());
        $this->sendMail();

    }

    /**
     * @see \Src\RouterBoard\IRouterBoardBackup::backupOneRouterBoard()
     * @param InputParser $input
     * @throws Exception
     */
    public function backupOneRouterBoard(InputParser $input)
    {
        if (!$inputArray = $input->getAddr())
            throw new Exception("Input array is empty!");

        foreach ($inputArray as $ipAddr) {
            if ($this->dbConnect->checkExistIP($ipAddr['addr'])) {
                $data = $this->dbConnect->getOneIP($ipAddr['addr']);
                if (is_null($data[0]['port']))
                    $data[0]['port'] = $this->config['routerboard']['ssh-port'];
                if ($this->ssh->getBackupFile($data[0]['addr'], $data[0]['port'], $this->rootDir, $this->folder, $data[0]['identity'])) {
                    // push both to the repository
                    $this->doGitLabPush($data[0]['addr'], $data[0]['identity'], 'backup', 'base64');
                    $this->doGitLabPush($data[0]['addr'], $data[0]['identity'], 'rsc', 'text');
                    $this->logger->log("Backup of the router " . $data[0]['addr'] . " has been sucessfully.");
                    $this->dbConnect->updateBackupTime($data[0]['addr']);
                    continue;
                }
            }
            $this->logger->log('IP addresses: ' . $ipAddr['addr'] . ' does not exist in the database! Add this IP address first.', $this->logger->setError());
        }
        $this->sendMail();
    }

    /**
     * GitLab Push File
     * @param string $addr
     * @param string $identity
     * @param string $extension
     * @param string $type
     */
    private function doGitLabPush($addr, $identity, $extension, $type)
    {
        $rbFolder = $identity . '_' . $addr . DIRECTORY_SEPARATOR;
        $message = 'backup/change time ' . date("Y-m-d H:i.s") . ' type = ' . $type;
        if ($type == 'base64')
            $content = base64_encode(file_get_contents($this->folder . $rbFolder . $this->filename . '.' . $extension));
        else
            $content = file_get_contents($this->folder . $rbFolder . $this->filename . '.' . $extension);
        $this->gitlab->sendFile(
            $rbFolder . $this->filename . '.' . $extension,
            $content,
            'master',
            $message
        );
    }

    /**
     * Check if project with $name does exist in repository, if not try create it
     *
     * @param string $name
     * @throws Exception
     */
    protected function checkExistProject($name)
    {
        if (!$this->gitlab->checkProjectName()) {
            $this->logger->log("Project '" . $name . "' does not exist in repo. Creating new ...", $this->logger->setNotice());
            if ($this->gitlab->createProject()) {
                $this->logger->log("Project '" . $this->config['gitlab']['project-name'] . "' has been created successfully.", $this->logger->setNotice());
                return;
            }
            throw new Exception("Can not create new project in GitLab!");
        }
    }

    /**
     * Check if group with $name does exist in repository, if not try create it
     *
     * @param string $name
     * @throws Exception
     */
    protected function checkExistGroup($name)
    {
        if (!$this->gitlab->checkGroupName()) {
            $this->logger->log("Group '" . $name . "' does not exist in repo. Creating new ...", $this->logger->setNotice());
            if ($this->gitlab->createGroup()) {
                $this->logger->log("Group '" . $this->config['gitlab']['group-name'] . "' has been created successfully.", $this->logger->setNotice());
                return;
            }
            throw new Exception("Can not create new group in GitLab!");
        }
    }


    /**
     * Send email with error if any
     */
    private function sendMail()
    {
        if ($this->config['mail']['sendmail'] && $this->logger->isMail())
            $this->logger->send($this->config['mail']['email-from'], $this->config['mail']['email-to']);
    }


}

