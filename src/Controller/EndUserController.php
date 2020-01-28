<?php

namespace App\Controller;

use App\Entity\EndUser;
use App\Exception\ResourceValidationException;
use App\Exception\ResourceAccessNotAuthorized;
use App\Representation\EndUsers;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface as ATM;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use App\Tools\Tools;

/**
 * EndUser controller.
 * @Route("/api", name="api_")
 */
class EndUserController extends AbstractFOSRestController
{
    // Access token manager
    private $atm;
    private $tools;

    public function __construct(ATM $accessTokenManager, Tools $tools)
    {
        $this->atm = $accessTokenManager;
        $this->tools = $tools;
    }

    // Returns the current authenticated client, if any.
    private function getAuthClient()
    {
        $oauthToken = $this->get('security.token_storage')->getToken();
        $apiAccessToken = $this->atm->findTokenBy(['token' => $oauthToken->getToken()]);
        $apiClient = $apiAccessToken->getClient();

        return $apiClient;
    }

    // Checks if an EndUser belongs to the current authenticated client.
    // If not, throws an appropriate custom exception.
    private function checkEndUserOwner($endUser)
    {
        if ($endUser->getClient() !== self::getAuthClient()) {
            $message = 'You are not authorized to access this resource.';

            throw new ResourceAccessNotAuthorized($message);
        }
    }

    /**
     * @Rest\Get("/end-users", name="end_user_list")
     * @Rest\QueryParam(
     *     name="lastname",
     *     requirements={
     *         "rule" = "[a-zA-Z]+",
     *         "error_message" = "'lastname' must contain only letters."
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) The end user's lastname to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements={
     *         "rule" = "asc|desc",
     *         "error_message" = "'order' must be 'asc' or 'desc'"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements={
     *         "rule" = "\d+",
     *         "error_message" = "'limit' must be an integer greater than 0"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) Max number of products per page."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements={
     *         "rule" = "\d+",
     *         "error_message" = "'page' must be an integer greater than 0"
     *     },
     *     strict=true,
     *     nullable=true,
     *     description="(optional) The requested paginated page."
     * )
     * @Rest\View
     * @SWG\Get(
     *     summary="Get a list of end users",
     *     description="Retrieve a list of end users.",
     *     operationId="list",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a paginated list of end users."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Parameter(
     *         name="lastname",
     *         in="query",
     *         type="string",
     *         description="(optional) The end user's lastname to search for."
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         in="query",
     *         type="integer",
     *         description="(optional) The maximum number of products per page."
     *     ),
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         type="integer",
     *         description="(optional) The requested paginated page."
     *     )
     * )
     * @SWG\Tag(name="End users")
     * @Security(name="Bearer")
     */
    public function list(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository(EndUser::class)->search(
            self::getAuthClient(),
            $paramFetcher->get('lastname'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('page')
        );

        return $this->tools->setCache($this, 300, new EndUsers($pager));
    }

    // We use a custom ParamConverter to display a custom "not found" message:
    // App/ParamConverter/EndUserParamConverter.php
    /**
     * @Rest\Get(
     *     path = "/end-users/{id}",
     *     name = "end_user_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * @SWG\Get(
     *     summary="Get a single end user",
     *     description="Retrieve a single end user.",
     *     operationId="show",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns an end user's details."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="End user not found."
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The id of the end user to be read."
     *     )
     * )
     * @SWG\Tag(name="End users")
     * @Security(name="Bearer")
     */
    public function show(EndUser $endUser)
    {
        self::checkEndUserOwner($endUser);

        return $this->tools->setCache($this, 600, $endUser);
    }

    /**
     * @Rest\Post("/end-users")
     * @ParamConverter(
     *     "endUser",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"creation", "EndUser"}}}
     * )
     * @Rest\View(StatusCode = 201)
     * @SWG\Post(
     *     summary="Create an end user",
     *     description="Create a new end user.",
     *     operationId="create",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=201,
     *         description="End user created."
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="The JSON sent contains invalid data."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     )
     * )
     * @SWG\Tag(name="End users")
     * @Security(name="Bearer")
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
     * @Rest\Put(
     *     path = "/end-users/{id}",
     *     name = "end_user_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter(
     *     "newEndUser",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"update", "EndUser"}}}
     * )
     * @Rest\View(StatusCode = 200)
     * @SWG\Put(
     *     summary="Update an end user",
     *     description="Update a existing end user.",
     *     operationId="update",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="End user updated."
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="The JSON sent contains invalid data."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="End user not found."
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The id of the end user to be updated."
     *     )
     * )
     * @SWG\Tag(name="End users")
     * @Security(name="Bearer")
     */
    public function update(
        EndUser $endUser,
        EndUser $newEndUser,
        ValidatorInterface $validator
    ) {
        self::checkEndUserOwner($endUser);

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

        if (! empty($repository->stringValExistsForOtherId($fieldName, $value, $id))) {
            $error = new ConstraintViolation($message, '', [], $endUser, 'email', '');
            return $error;
        }

        return;
    }

    /**
     * @Rest\Delete(
     *     path = "/end-users/{id}",
     *     name = "end_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 204)
     * @SWG\Delete(
     *     summary="Delete an end user",
     *     description="Delete an end user.",
     *     operationId="delete",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=204,
     *         description="End user deleted."
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="OAuth2 authentication required."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="User not found."
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         description="The id of the end user to be deleted."
     *     )
     * )
     * @SWG\Tag(name="End users")
     * @Security(name="Bearer")
     */
    public function delete(EndUser $endUser)
    {
        self::checkEndUserOwner($endUser);

        $this->getDoctrine()->getManager()->remove($endUser);
        $this->getDoctrine()->getManager()->flush();

        return;
    }
}
