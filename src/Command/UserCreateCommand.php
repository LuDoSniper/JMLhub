<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Add a short description for your command',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public UserPasswordHasherInterface $hasher
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email of the new User')
            ->addArgument('username', InputArgument::OPTIONAL, 'Username of the new User')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password of the new User')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');

        try {
            $user = new User();
            $password = $this->hasher->hashPassword($user, $plainPassword);
            $user
                ->setEmail($email)
                ->setUsername($username)
                ->setPassword($password)
            ;

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success('User created');

        return Command::SUCCESS;
    }
}
