<?php


namespace Firebase\Tests\Testing;


use Faker\Factory;

class RandomUser
{
    private $uid;

    private $email;

    public function __construct(?string $uid, ?string $email)
    {
        $this->uid = $uid;
        $this->email = $email;
    }

    static function create() {
        $faker = Factory::create();
        $uid = str_replace('-', '', $faker->uuid);
        $email = strtolower(
            sprintf(
                'test%s@example.%s.com',
                substr($uid, 0, 12),
                substr($uid, 0, 12)
            )
        );
        return new RandomUser($uid, $email);
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
