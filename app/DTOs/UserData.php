<?php


namespace App\DTOs;

class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password,
        public string $role,

    ){}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            role: $data['role'],
     
        );
    }
}