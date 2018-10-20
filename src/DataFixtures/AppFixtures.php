<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'jacek',
            'email' => 'jacqu82@wp.pl',
            'password' => 'qwerty',
            'fullName' => 'Jacek Wesołowski',
            'roles' => [User::ROLE_ADMIN, User::ROLE_USER]
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob12345',
            'fullName' => 'Rob Smith',
            'roles' => [User::ROLE_USER]
        ],
        [
            'username' => 'marry_gold',
            'email' => 'marry_gold@gold.com',
            'password' => 'marry12345',
            'fullName' => 'Marry Gold',
            'roles' => [User::ROLE_USER]
        ],
    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++) {
            $microPost = (new MicroPost())
                ->setText(self::POST_TEXT[rand(0, count(self::POST_TEXT) - 1)]);
            $date = new \DateTime();
            $date->modify('-' . rand(0, 10) . ' day');
            $microPost
                ->setTime($date)
                ->setUser($this->getReference(self::USERS[rand(0, count(self::USERS) - 1)]['username']));
            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userData) {
            $user = (new User())
                ->setUsername($userData['username'])
                ->setFullName($userData['fullName'])
                ->setEmail($userData['email'])
                ->setRoles($userData['roles']);
            $user
                ->setPassword($this->passwordEncoder->encodePassword($user, $userData['password']))
                ->setCreatedAt(new \DateTime());
            $this->addReference($userData['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }
}