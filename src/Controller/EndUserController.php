<?php

namespace App\Controller;

use App\Entity\EndUser;
use App\Exception\ResourceValidationException;
use App\Representation\EndUsers;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * EndUser controller.
 * @Route("/api", name="api_")
 */
class EndUserController extends FOSRestController
{
    private function getAuthClient()
    {
        $oauthToken = $this->get('security.token_storage')->getToken();
        $apiAccessToken = $this->get('fos_oauth_server.access_token_manager.default')
            ->findTokenBy(['token' => $oauthToken->getToken()]);
        $apiClient = $apiAccessToken->getClient();
        return $apiClient;
        //return;

        /*
        $tokenManager = $this->container
            ->get('fos_oauth_server.access_token_manager.default');
        $accessToken = $tokenManager->findTokenByToken(
            $this->container->get('security.context')->getToken()->getToken()
        );
        return $accessToken->getClient();
        */
    }

    /**
     * @Rest\Get("/end-users", name="api_end_user_list")
     * @Rest\QueryParam(
     *     name="lastname",
     *     requirements="[a-zA-Z]+",
     *     nullable=true,
     *     description="The end user lastname to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Max number of products per page."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="The requested paginated page."
     * )
     * @Rest\View
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        self::getAuthClient();
        $pager = $this->getDoctrine()->getRepository(EndUser::class)->search(
            //self::getAuthClient(),
            $paramFetcher->get('lastname'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('page')
        );

        return new EndUsers($pager);
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
     * @ParamConverter(
     *     "endUser",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"creation", "EndUser"}}}
     * )
     */
    public function create(EndUser $endUser, ConstraintViolationList $violations)
    {
        // The end user email address and phone number must be unique.
        // This is automatically checked thanks to the @UniqueEntity(...)
        // constraint set in the EndUser entity class, in addition to the
        // "@Assert\NotBlank()" attributes.

        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Field %s: %s ",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }

            throw new ResourceValidationException($message);
        }

        $endUser->setCreationDate(new \DateTime());
        $endUser->setClient(self::getAuthClient());

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
     * @ParamConverter(
     *     "newEndUser",
     *     converter="fos_rest.request_body"
     * )
     */
    public function update(
        EndUser $endUser,
        EndUser $newEndUser,
        ValidatorInterface $validator
    ) {
        // Because of the following issue, we can't use the validator
        // the same way we did in the create() method :
        // https://github.com/FriendsOfSymfony/FOSRestBundle/issues/1751
        // The validator finds a duplicate unique attribute in the record
        // we're currently modifying!

        // First, validating all constraints not belonging to a specific
        // validation group. Using the entity class name as validation
        // group name is almost equal to using the "Default" validation group.
        // In our case, we want to ignore the "creation" group.
        $errors = $validator->validate($newEndUser, null, ['EndUser']);

        // Manually validating the title & phoneNumber uniqueness:

        // Adding an error to errors
        // https://stackoverflow.com/questions/29688049/add-custom-error-to-symfony-validator
        if (null !== $error = self::checkUniqueEmail($newEndUser->getEmail(), $endUser)) {
            $errors->add($error);
        }
        if (null !== $error = self::checkUniquePhone($newEndUser->getPhoneNumber(), $endUser)) {
            $errors->add($error);
        }

        if (count($errors)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($errors as $error) {
                $message .= sprintf(
                    "Field %s: %s ",
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
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

    public function checkUniqueEmail($value, $endUser)
    {
        $message = 'This email address already exists.';

        return self::checkUniqueValue('email', $value, $endUser, $message);
    }

    public function checkUniquePhone($value, $endUser)
    {
        $message = 'This phone number already exists.';

        return self::checkUniqueValue('phoneNumber', $value, $endUser, $message);
    }

    public function checkUniqueValue($fieldName, $value, $endUser, $message)
    {
        $repository = $this->getDoctrine()->getRepository(EndUser::class);
        $id = $endUser->getId();

        if (! empty($repository->stringValueExistsForOtherId($fieldName, $value, $id))) {
            $error = new ConstraintViolation($message, '', [], $endUser, 'email', '');
            return $error;
        }

        return;
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
