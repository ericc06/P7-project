<?php

namespace App\Controller;

use App\Entity\EndUser;
use App\Exception\ResourceValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;

/**
 * EndUser controller.
 * @Route("/api", name="api_")
 */
class EndUserController extends FOSRestController
{
    /**
     * @Rest\Get("/end-users", name="api_end_user_list")
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        $endUsers = $this->getDoctrine()->getRepository(EndUser::class)->findAll();
        $data = $this->get('jms_serializer')->serialize($endUsers, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;

        /*$pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
        */
    }

    /**
     * @Rest\Get(
     *     path = "/end-users/{id}",
     *     name = "api_end_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function show(EndUser $endUser)
    {
        return $endUser;
    }

    /**
     * @Rest\Post("/end-users")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("endUser", converter="fos_rest.request_body")
     */
    public function create(EndUser $endUser, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $endUser->setCreationDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $em->persist($endUser);
        $em->flush();

        return $endUser;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/end-users/{id}",
     *     name = "api_end_user_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newEndUser", converter="fos_rest.request_body")
     */
    public function update(EndUser $endUser, EndUser $newEndUser, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $endUser->setFirstName($newEndUser->getFirstName());
        $endUser->setLastName($newEndUser->getLastName());
        $endUser->setEmail($newEndUser->getEmail());
        $endUser->setPhoneNumber($newEndUser->getPhoneNumber());
        $endUser->setLastUpdateDate(new \DateTime());

        $this->getDoctrine()->getManager()->flush();

        return $endUser;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/end-users/{id}",
     *     name = "api_end_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function delete(EndUser $endUser)
    {
        $this->getDoctrine()->getManager()->remove($endUser);
        $this->getDoctrine()->getManager()->flush();

        return;
    }
}
