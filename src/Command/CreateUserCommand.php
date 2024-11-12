<?php

namespace App\Command;

use App\Entity\Authentication\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'CreateUserCommand',
    description: 'Create an user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public UserPasswordHasherInterface $hasher)
    {

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email\'s user')
            ->addArgument("username", InputArgument::REQUIRED, 'Username\'s user')
            ->addArgument('password', InputArgument::REQUIRED, 'Password\'s user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if ($email && $username && $password) {
            $user = new User();
            $hashedPassword = $this->hasher->hashPassword($user, $password);
            $user
                ->setEmail($email)
                ->setPassword($hashedPassword)
                ->setUsername($username)
            ;



            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            $io->error('Invalid arguments');
            return Command::INVALID;
        }

        $io->success('The user have been successfully added to database');

        return Command::SUCCESS;
    }
}
