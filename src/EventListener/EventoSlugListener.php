<?php

namespace App\EventListener;

use App\Entity\Evento;
use App\Repository\EventoRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Gera o slug do evento a partir do título ao criar. Não há preUpdate: o endereço não muda na edição.
 */
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Evento::class)]
final class EventoSlugListener
{
    private readonly AsciiSlugger $slugger;

    public function __construct(private readonly EventoRepository $eventos)
    {
        // Sem mapa de símbolos: "&" é removido em vez de virar "and"
        $this->slugger = new AsciiSlugger('pt', []);
    }

    public function prePersist(Evento $evento): void
    {
        if (null !== $evento->getSlug()) {
            return;
        }

        $evento->setSlug($this->gerarSlugUnico((string) $evento->getTitulo()));
    }

    public function gerarSlug(string $titulo): string
    {
        $slug = $this->slugger->slug($titulo)->lower()->toString();

        return '' === $slug ? 'evento' : $slug;
    }

    private function gerarSlugUnico(string $titulo): string
    {
        $base = $this->gerarSlug($titulo);
        $slug = $base;
        $sufixo = 2;

        while ($this->eventos->slugExiste($slug)) {
            $slug = $base.'-'.$sufixo++;
        }

        return $slug;
    }
}
