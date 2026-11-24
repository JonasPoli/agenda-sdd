<?php

namespace App\Repository;

use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evento>
 */
class EventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evento::class);
    }

    /**
     * Home: eventos publicados a partir de hoje às 00:00 (horário de Brasília), do mais próximo ao mais distante.
     *
     * @return list<Evento>
     */
    public function publicadosAPartirDeHoje(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('c')
            ->join('e.categoria', 'c')
            ->where('e.publicado = true')
            ->andWhere('e.inicio >= :hoje')
            ->setParameter('hoje', new \DateTimeImmutable('today'), Types::DATETIME_IMMUTABLE)
            ->orderBy('e.inicio', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Painel: os próximos eventos a partir de agora, publicados ou não.
     *
     * @return list<Evento>
     */
    public function proximos(int $limite): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('c')
            ->join('e.categoria', 'c')
            ->where('e.inicio >= :agora')
            ->setParameter('agora', new \DateTimeImmutable('now'), Types::DATETIME_IMMUTABLE)
            ->orderBy('e.inicio', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }

    /**
     * Admin: todos os eventos, do início mais recente para o mais antigo.
     *
     * @return list<Evento>
     */
    public function listarParaAdmin(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('c')
            ->join('e.categoria', 'c')
            ->orderBy('e.inicio', 'DESC')
            ->addOrderBy('e.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Detalhe público: só eventos publicados, de qualquer data.
     */
    public function buscarPublicadoPorSlug(string $slug): ?Evento
    {
        return $this->createQueryBuilder('e')
            ->addSelect('c')
            ->join('e.categoria', 'c')
            ->where('e.slug = :slug')
            ->andWhere('e.publicado = true')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function slugExiste(string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }
}
