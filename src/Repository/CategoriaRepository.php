<?php

namespace App\Repository;

use App\Entity\Categoria;
use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categoria>
 */
class CategoriaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categoria::class);
    }

    /**
     * Todas as categorias em ordem de nome, cada uma com a quantidade de eventos.
     *
     * @return list<array{categoria: Categoria, totalEventos: int}>
     */
    public function listarComTotalDeEventos(): array
    {
        $linhas = $this->createQueryBuilder('c')
            ->select('c AS categoria', 'COUNT(e.id) AS totalEventos')
            ->leftJoin('c.eventos', 'e')
            ->groupBy('c.id')
            ->orderBy('c.nome', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map(
            static fn (array $linha): array => [
                'categoria' => $linha['categoria'],
                'totalEventos' => (int) $linha['totalEventos'],
            ],
            $linhas,
        );
    }

    /**
     * Categorias ativas mais a categoria atual do evento em edição (mesmo inativa), em ordem de nome.
     */
    public function queryAtivasOuAtual(?Categoria $atual): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')->orderBy('c.nome', 'ASC');

        if (null !== $atual?->getId()) {
            return $qb->where('c.ativa = true OR c.id = :atual')->setParameter('atual', $atual->getId());
        }

        return $qb->where('c.ativa = true');
    }

    public function contarEventos(Categoria $categoria): int
    {
        return (int) $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Evento::class, 'e')
            ->where('e.categoria = :categoria')
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
