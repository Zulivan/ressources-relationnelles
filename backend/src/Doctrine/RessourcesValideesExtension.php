<?php
namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Ressource;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

use App\Service\LoggedUser;

final class RessourcesValideesExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $loggedUser;

    public function __construct(private readonly Security $security, LoggedUser $loggedUser)
    {
        $this->loggedUser = $loggedUser;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $mod = $this->loggedUser->isModerator();

        if (Ressource::class !== $resourceClass || $mod) {

        }else{
        // retourne seulement les ressources validées
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.validee = :valide', $rootAlias));

        if($this->loggedUser->get()){
            $queryBuilder->orWhere(sprintf('%s.createur = :user', $rootAlias));
            $queryBuilder->setParameter('user', $this->loggedUser->get());
        }

        $queryBuilder->setParameter('valide', true);
        }
    }
}