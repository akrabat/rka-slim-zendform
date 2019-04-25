# Zend\Form integration with Slim 3

This service provider integrates Zend\Form into a Slim 3 application.


## Usage

1. `composer require slim/twig-view`
2. `composer require akrabat/rka-slim-zendform`
3. Register Twig-View as per the [README](https://github.com/slimphp/Twig-View/)
3. Register the `FormProvider` in index.php:

        $app->getContainer()->register(new RKA\Form\FormProvider);

4. Create a form:

        <?php
        namespace RKA;

        use Zend\Form\Form;
        use Zend\InputFilter\InputFilterProviderInterface;

        class ExampleForm extends Form implements InputFilterProviderInterface
        {
            public function init()
            {
                $this->add([
                    'name' => 'email',
                    'options' => [
                        'label' => 'Email address',
                    ],
                    'attributes' => [
                        'id'       => 'email',
                        'class'    => 'form-control',
                        'required' => 'required',
                    ],
                ]);

                $this->add([
                    'name' => 'submit',
                    'type' => 'button',
                    'options' => [
                        'label' => 'Go!',
                    ],
                    'attributes' => [
                        'class' => 'btn btn-default',
                    ],
                ]);
            }

            public function getInputFilterSpecification()
            {
                return [
                    'email' => [
                        'required' => true,
                        'filters'  => [
                            ['name' => 'StringTrim'],
                            ['name' => 'StripTags'],
                        ],
                        'validators' => [
                            ['name' => 'EmailAddress'],
                        ],
                    ],
                ];
            }
        }


5. Example action:

        $app->map(['GET', 'POST'], '/', function ($request, $response) {
            $sm = $this['serviceManager'];
            $formElementManager = $sm->get('FormElementManager');
            $form = $formElementManager->get("RKA\ExampleForm");

            if ($request->isPost()) {
                $data = $request->post();
                $form->setData($data);
                $isValid = $form->isValid();
                if ($form->isValid()) {
                    echo "Success!";
                    exit;
                }
            }

            $this['view']->render($response, 'home.twig', array(
                'form' => $form
            ));
            return $response;
        });

