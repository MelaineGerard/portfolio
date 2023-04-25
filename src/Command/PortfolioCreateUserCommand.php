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
    name: 'portfolio:create:user',
    description: 'Add a short description for your command',
)]
class PortfolioCreateUserCommand extends Command
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    )
    {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $io->ask('Quel est votre nom  ?');
        $email = $io->ask('Quel est votre email ?');
        $password = $io->askHidden('Quel est votre mot de passe ?');
        $avatar = $io->ask('Quel est votre avatar ?');

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $password = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($password);

        $user->setAvatar($avatar);

        $this->em->persist($user);
        $this->em->flush();


        return Command::SUCCESS;
    }
}
