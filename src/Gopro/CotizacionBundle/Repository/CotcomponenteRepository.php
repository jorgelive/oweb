<?php

namespace Gopro\CotizacionBundle\Repository;
use Gopro\UserBundle\Entity\User;
use SensioLabs\Security\Exception\HttpException;

/**
 * CotcomponenteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CotcomponenteRepository extends \Doctrine\ORM\EntityRepository
{
    public function findCalendarAceptado($data)
    {
        if (!$data['user'] instanceof User) {
            throw new HttpException(500, 'El dato de usuario no es instancia de la clase GoproUserbundle:Entity:User.');
        } else {
            $user = $data['user'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('GoproCotizacionBundle:Cotcomponente', 'c')
            ->innerJoin('c.cotservicio', 'cs')
            ->innerJoin('cs.cotizacion', 'cot')
            ->where('c.fechahorainicio BETWEEN :firstDate AND :lastDate')
            ->andWhere('cot.estadocotizacion = 3');


        $qb->setParameter('firstDate', $data['from'])
            ->setParameter('lastDate', $data['to']);

        return $qb->getQuery()->getResult();

    }

    public function findCalendarAceptadoEfectuado($data)
    {
        if (!$data['user'] instanceof User) {
            throw new HttpException(500, 'El dato de usuario no es instancia de la clase GoproUserbundle:Entity:User.');
        } else {
            $user = $data['user'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from('GoproCotizacionBundle:Cotcomponente', 'c')
            ->innerJoin('c.cotservicio', 'cs')
            ->innerJoin('cs.cotizacion', 'cot')
            ->where('c.fechahorainicio BETWEEN :firstDate AND :lastDate')
            ->andWhere('cot.estadocotizacion IN (:estados)');


        $qb->setParameter('firstDate', $data['from'])
            ->setParameter('lastDate', $data['to'])
            ->setParameter('estados', [3, 4]);

        return $qb->getQuery()->getResult();

    }
}
