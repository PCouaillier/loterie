<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 11:16
 */

namespace App\Service;

use PDO;

class SqliteDbCreator
{
    /** @var PDO $pdo */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createSqliteDb()
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $users = [
            [
                'username' => 'admin',
                'mail' => 'admin@admin.admin',
                'password' => password_hash('admin', PASSWORD_BCRYPT),
                'canCreateRoom' => true
            ],
            [
                'username' => 'test',
                'mail' => 'test@test.test',
                'password' => password_hash('password', PASSWORD_BCRYPT),
                'canCreateRoom' => false
            ]
        ];

        $this->createSqliteSchemaIfNotExists();


        if ($this->isEmpty('User')) {

            $this->pdo->beginTransaction();

            foreach ($users as $user) {
                $this->pdo
                    ->prepare('INSERT INTO User (username, mail, password, canCreateRoom) VALUES (?, ?, ?, ?);')
                    ->execute([$user['username'], $user['mail'], $user['password'], $user['canCreateRoom']]);
            }
            $adminId = intval($this->pdo->lastInsertId());
            $this->pdo->prepare('INSERT INTO `Room` (`engine`, `owner`, `begin`, `end`) VALUES (?, ?, ?, ?)')
                ->execute([
                            'guillaumeengine',
                            $adminId,
                            '2017-12-01 00:00:00',
                            '2017-12-25 00:00:00'
                        ]);
            $this->pdo->prepare('INSERT INTO `Room` (`engine`, `owner`, `begin`, `end`) VALUES (?, ?, ?, ?)')
                ->execute([
                        'guillaumeengine',
                        $adminId,
                        '2017-11-20 00:00:00',
                        '2017-12-20 00:00:00'
                ]);
            $this->pdo->commit();
        }
    }

    private function createSqliteSchemaIfNotExists()
    {
        $pdo = $this->pdo;
        $pdo->beginTransaction();

        $pdo->exec('CREATE TABLE IF NOT EXISTS `User` ( `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `username` TEXT NOT NULL UNIQUE, `mail` TEXT NOT NULL UNIQUE, `password` TEXT NOT NULL, `displayName` TEXT ,`canCreateRoom`	INTEGER NOT NULL DEFAULT 0);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS `Room` ( `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `engine` TEXT NOT NULL, `owner` INTEGER NOT NULL, `begin` TEXT, `end` TEXT);');
        $pdo->exec('CREATE TABLE IF NOT EXISTS `UserInRoom` ( `user` INTEGER NOT NULL, `room` INTEGER NOT NULL, PRIMARY KEY(`user`,`room`) )');
        $pdo->exec('CREATE TABLE IF NOT EXISTS `UserRollInRoom` ( `user` INTEGER NOT NULL, `room` INTEGER NOT NULL, `date` TEXT NOT NULL, `points` INTEGER NOT NULL, PRIMARY KEY(`user`,`room`,`date`) )');
        $pdo->exec('CREATE TABLE IF NOT EXISTS `Gift` ( `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, `name` TEXT NOT NULL, `cost` INTEGER NOT NULL, `quantity` INTEGER NOT NULL, `room` INTEGER NOT NULL )');
        $pdo->exec('CREATE INDEX IF NOT EXISTS `giftRoomAssoc` ON `Gift` ( `room` )');
        $pdo->exec('CREATE INDEX IF NOT EXISTS `UserRollDateIndex` ON `UserRollInRoom` ( `date` DESC);');

        $pdo->commit();
    }

    private function isEmpty(string $tableName): bool
    {
        $st = $this->pdo->query("SELECT * FROM `$tableName` LIMIT 2;");
        return count($st->fetchAll()) == 0;
    }
}