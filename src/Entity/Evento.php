<?php

namespace App\Entity;

use App\Repository\EventoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventoRepository::class)]
#[ORM\Table(name: 'evento')]
#[ORM\UniqueConstraint(name: 'uniq_evento_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_evento_publicado_inicio', columns: ['publicado', 'inicio'])]
#[UniqueEntity(fields: ['slug'], message: 'Já existe um evento com este endereço.')]
class Evento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank(message: 'Informe o título.')]
    #[Assert\Length(max: 120, maxMessage: 'O título pode ter no máximo 120 caracteres.')]
    private ?string $titulo = null;

    /**
     * Gerado do título no prePersist (EventoSlugListener) e nunca alterado depois.
     */
    #[ORM\Column(length: 140)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'O resumo pode ter no máximo 255 caracteres.')]
    private ?string $resumo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descricao = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'Informe a data e hora de início.')]
    private ?\DateTimeImmutable $inicio = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Length(max: 120, maxMessage: 'O local pode ter no máximo 120 caracteres.')]
    private ?string $local = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $publicado = false;

    #[ORM\ManyToOne(targetEntity: Categoria::class, inversedBy: 'eventos')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    #[Assert\NotNull(message: 'Selecione a categoria.')]
    private ?Categoria $categoria = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): static
    {
        $this->titulo = null === $titulo ? null : trim($titulo);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getResumo(): ?string
    {
        return $this->resumo;
    }

    public function setResumo(?string $resumo): static
    {
        $this->resumo = self::textoOuNulo($resumo);

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->descricao = self::textoOuNulo($descricao);

        return $this;
    }

    public function getInicio(): ?\DateTimeImmutable
    {
        return $this->inicio;
    }

    public function setInicio(?\DateTimeImmutable $inicio): static
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): static
    {
        $this->local = self::textoOuNulo($local);

        return $this;
    }

    public function isPublicado(): bool
    {
        return $this->publicado;
    }

    public function setPublicado(bool $publicado): static
    {
        $this->publicado = $publicado;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    private static function textoOuNulo(?string $texto): ?string
    {
        if (null === $texto) {
            return null;
        }

        $texto = trim($texto);

        return '' === $texto ? null : $texto;
    }
}
