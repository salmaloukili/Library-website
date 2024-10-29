<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table('feedback')]
class Feedback {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", nullable: false)]
    private ?int $id = null;


    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $email = null;


    #[ORM\Column(name: "phoneNumber", type: "string", length: 255, nullable: false)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $name = null;


     #[ORM\Column(type: "text", nullable: true)]
    private ?string $message = null;

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getPhoneNumber(): ?string {
        return $this->phoneNumber;
    }


    public function getName(): ?string {
        return $this->name;
    }


    public function getMessage(): ?string {
        return $this->message;
    }

    public function setEmail(string $email): Feedback {
        $this->email = $email;
        return $this;
    }

    public function setPhoneNumber(string $phoneNumber): Feedback {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function setName(string $name): Feedback {
        $this->name = $name;
        return $this;
    }

    public function setMessage(string $message): Feedback {
        $this->message = $message;
        return $this;
    }


}
