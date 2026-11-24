<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
#[ORM\Table(name: 'categoria')]
#[ORM\UniqueConstraint(name: 'uniq_categoria_nome', columns: ['nome'])]
#[UniqueEntity(fields: ['nome'], message: 'Já existe uma categoria com este nome.')]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(message: 'Informe o nome.')]
    #[Assert\Length(max: 60, maxMessage: 'O nome pode ter no máximo 60 caracteres.')]
    private ?string $nome = null;

    #[ORM\Column(length: 7)]
    #[Assert\NotBlank(message: 'Informe a cor no formato #RRGGBB.')]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/', message: 'Informe a cor no formato #RRGGBB.')]
    private ?string $cor = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $ativa = true;

    /**
     * @var Collection<int, Evento>
     */
    #[ORM\OneToMany(targetEntity: Evento::class, mappedBy: 'categoria')]
    private Collection $eventos;

    public function __construct()
    {
        $this->eventos = new ArrayCollection();
    }

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

    public function getCor(): ?string
    {
        return $this->cor;
    }

    public function setCor(?string $cor): static
    {
        $this->cor = null === $cor ? null : strtoupper(trim($cor));

        return $this;
    }

    public function isAtiva(): bool
    {
        return $this->ativa;
    }

    public function setAtiva(bool $ativa): static
    {
        $this->ativa = $ativa;

        return $this;
    }

    /**
     * @return Collection<int, Evento>
     */
    public function getEventos(): Collection
    {
        return $this->eventos;
    }

    public function __toString(): string
    {
        return (string) $this->nome;
    }
}
