<?php

declare(strict_types=1);

namespace App\List\VideoGameList;

use App\Model\ValueObject\Direction;
use App\Model\ValueObject\Sorting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTargetedValueResolver('pagination')]
final readonly class PaginationValueResolver implements ValueResolverInterface
{
    /**
     * Resolves the pagination parameters from the request.
     * @return Pagination[] An array containing a single Pagination object.
     * @throws \InvalidArgumentException If the argument type is not Pagination.
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $argumentType = $argument->getType();

        if (Pagination::class !== $argumentType) {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type "%s", got "%s"',
                Pagination::class,
                $argumentType ?? 'null'
            ));
        }

        return [new Pagination(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10),
            Sorting::tryFromName((string)$request->query->get('sorting')) ?? Sorting::ReleaseDate,
            Direction::tryFromName((string)$request->query->get('direction')) ?? Direction::Ascending
        )];
    }
}
