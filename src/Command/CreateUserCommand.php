<?php
/**
 * Created by PhpStorm.
 * User: adminHOC
 * Date: 12/02/2019
 * Time: 16:37
 */

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;




class CreateUserCommand extends Command
{


    protected static $defaultName = 'app:create-user';

    private $users;

    public function __construct(UserRepository $users)
    {

        $this->users = $users;
        parent::__construct();
    }


    /**
     * Configurations
     */
    protected function configure()
    {
        $this

            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...')
            ->addArgument('username', InputArgument::REQUIRED, 'user name login')
            ->addArgument('password', InputArgument::REQUIRED, 'user password')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');


        if (!empty($username) && !empty($plainPassword)) {
            $this->users->createAdminFromCommand(
                $username,
                $plainPassword
            );

            $io->success(sprintf("Your username : ".
                $username.
                " & your password : "
                .$plainPassword));

            $io->note("pour remplacer l'utilisateur,  veuillez vous connecter à phpmyadmin et le supprimer puis rejouez la commande\"");

        }else{
            throw new \RuntimeException("<options=bold>you password or your login is empty</>");
        }


    }


}