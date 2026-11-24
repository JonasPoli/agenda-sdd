<?php

namespace App\Command;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:criar-admin',
    description: 'Cria um administrador (ROLE_ADMIN) pedindo nome, e-mail e senha.',
)]
final class CriarAdminCommand extends Command
{
    private const TAMANHO_MINIMO_SENHA = 8;

    public function __construct(
        private readonly UsuarioRepository $usuarios,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Novo administrador');

        $nome = $io->ask('Nome', null, static function (?string $valor): string {
            $valor = trim((string) $valor);
            if ('' === $valor) {
                throw new \RuntimeException('Informe o nome.');
            }
            if (mb_strlen($valor) > 120) {
                throw new \RuntimeException('O nome pode ter no máximo 120 caracteres.');
            }

            return $valor;
        });

        $email = $io->ask('E-mail', null, static function (?string $valor): string {
            $valor = mb_strtolower(trim((string) $valor));
            if (false === filter_var($valor, \FILTER_VALIDATE_EMAIL) || mb_strlen($valor) > 180) {
                throw new \RuntimeException('Informe um e-mail válido.');
            }

            return $valor;
        });

        if ($this->usuarios->emailExiste($email)) {
            $io->error('Já existe um usuário com este e-mail.');

            return Command::FAILURE;
        }

        $senha = $io->askHidden('Senha (mínimo de 8 caracteres)', static function (?string $valor): string {
            $valor = (string) $valor;
            if (mb_strlen($valor) < self::TAMANHO_MINIMO_SENHA) {
                throw new \RuntimeException(sprintf('A senha deve ter pelo menos %d caracteres.', self::TAMANHO_MINIMO_SENHA));
            }

            return $valor;
        });

        $usuario = (new Usuario())
            ->setNome($nome)
            ->setEmail($email)
            ->setRoles(['ROLE_ADMIN']);
        $usuario->setSenha($this->hasher->hashPassword($usuario, $senha));

        $this->em->persist($usuario);
        $this->em->flush();

        $io->success(sprintf('Administrador %s <%s> criado com sucesso.', $nome, $email));

        return Command::SUCCESS;
    }
}
