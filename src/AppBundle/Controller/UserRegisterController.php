<?php
/**
 * Created by PhpStorm.
 * User: ez
 */

namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\Repository\ContentTypeService;
use EzSystems\RepositoryForms\Data\Mapper\UserRegisterMapper;
use EzSystems\RepositoryForms\Form\Type\User\UserRegisterType;
use EzSystems\RepositoryForms\UserRegister\View\UserRegisterFormView;
use Symfony\Component\HttpFoundation\Request;

class UserRegisterController extends Controller
{


    private $contentActionDispatcher;



    /**
     * Displays and processes a user registration form.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception if the current user isn't allowed to register an account
     */
    public function registerAction(Request $request)
    {

        if( $this->container->get('security.context')
            ->isGranted('IS_AUTHENTICATED_FULLY') )
        {
            return $this->redirect('/'); // or everywhere
        }

        //Get siteaccess configuration
        $configResolver = $this->getConfigResolver();
        $userContenttype = $configResolver->getParameter( 'user_registration.contenttype', 'appbundle' );
        $userGroupid = $configResolver->getParameter( 'user_registration.group_id', 'appbundle' );
        $siteDefaultLanguage = $configResolver->getParameter('languages', null, null)[0];
        $formTemplate = $configResolver->getParameter( 'user_registration.templates.form', 'appbundle' );

        //to load the registration content type from a configured, injected content type identifier.
        $contentTypeLoaderService = $this->get('ezrepoforms.user_register.registration_content_type_loader.configurable');
        $contentTypeLoader = $contentTypeLoaderService->setParam('contentTypeIdentifier',$userContenttype);
        //Try:$contentTypeLoader->loadContentType

        //To load the registration user group from a configured, injected group ID
        $registrationGroupLoaderService = $this->get('ezrepoforms.user_register.registration_group_loader.configurable');
        $registrationGroupLoader = $registrationGroupLoaderService->setParam('groupId', $userGroupid);
        //Try:$userGroupObject->loadGroup()

        //Form data mapper for user registration / creation.
        $userRegisterMapper = new UserRegisterMapper($contentTypeLoader, $registrationGroupLoader);
        $userRegisterMapper->setParam('language',$siteDefaultLanguage);
        $data = $userRegisterMapper->mapToFormData();

        //Creates and returns a Form instance from the type of the form.
        $language = $data->mainLanguageCode;
        $form = $this->createForm(
            UserRegisterType::class,
            $data,
            ['languageCode' => $language]
        );


        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->contentActionDispatcher = $this->get('ezrepoforms.action_dispatcher.content');
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return $this->render($formTemplate, [
            'form' => $form->createView(),

        ]);

        //default : repository-forms/bundle/Controller/UserRegisterController.php
        // you should not change default template, don't do it ! override it  see above return;)
        return new UserRegisterFormView(
            null,
            ['form' => $form->createView()]
        );
    }

    public function registerConfirmAction()
    {

        return $this->render('user/register_confirmation.html.twig');

    }

}
