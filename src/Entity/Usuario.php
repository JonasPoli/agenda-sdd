<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\Table(name: 'usuario')]
#[ORM\UniqueConstraint(name: 'uniq_usuario_email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Já existe um usuário com este e-mail.')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Informe o nome.')]
    #[Assert\Length(max: 120, maxMessage: 'O nome pode ter no máximo 120 caracteres.')]
    private ?string $nome = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Informe o e-mail.')]
    #[Assert\Email(message: 'Informe um e-mail válido.')]
    #[Assert\Length(max: 180, maxMessage: 'O e-mail pode ter no máximo 180 caracteres.')]
    private ?string $email = null;

    /**
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * Hash da senha (nunca a senha em texto puro).
     */
    #[ORM\Column]
    private ?string $senha = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): static
    {
        $this->nome = null === $nome ? null : trim($nome);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = null === $email ? null : mb_strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->senha;
    }

    public function setSenha(string $hash): static
    {
        $this->senha = $hash;

        return $this;
    }

    /**
     * Evita guardar o hash da senha na sessão (Symfony 7.3+).
     */
    public function __serialize(): array
    {
        $dados = (array) $this;
        $dados["\0".self::class."\0senha"] = hash('crc32c', (string) $this->senha);

        return $dados;
    }

    public function eraseCredentials(): void
    {
    }
}
