<br />

<span id="readme-top"><span>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## Symfony Request Validator

`RequestValidator` is a Symfony bundle that simplifies the process of validating request data and generating request classes with validation rules. It integrates with Symfony's Validator component and provides an easy way to generate request classes with built-in validation constraints.


<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

### Installation

1. Install the package
   ```sh
   composer require thvvger/symfony-request-validator
   ```
2. Configure the Services :
   After installing the bundle, you need to register the services for the `FileGenerator` and `GenerateClassCommand`.

   Add the following configuration to your `config/services.yaml`:

    ```yaml
    # Register FileGenerator service
    Thvvger\RequestValidator\Services\FileGenerator:
        autowire: true
        autoconfigure: true
        arguments:
            $projectDir: '%kernel.project_dir%'  # Inject the project directory path

    # Register GenerateClassCommand as a console command
    Thvvger\RequestValidator\Command\GenerateClassCommand:
        autowire: true
        autoconfigure: true
        arguments:
            $fileGenerator: '@Thvvger\RequestValidator\Services\FileGenerator'  # Inject the FileGenerator service
        tags:
            - { name: 'console.command' }  # Register the command for Symfony's console
    ```

    - The `FileGenerator` service is configured with `autowire` and `autoconfigure` to automatically inject dependencies.
    - The `GenerateClassCommand` is registered as a console command, allowing you to execute it from the command line using `php bin/console generate:class`.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

## Generate a Request Class

To generate a request class, use the following command:

```shell
  php bin/console make:request TestRequest
```

This will generate a PHP file in the src/Request/ directory with a base structure for your request class.

## Example Generated Class
Here’s an example of a class generated after running the command:

```php
    namespace App\Request;
    
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;
    use Thvvger\RequestValidator\BaseRequest;
    
    class TestRequest extends BaseRequest
    {
        #[NotBlank]
        #[Type('string')]
        public readonly string $name;
        
        // add other properties
    }
```

## Example Usage in Your Controller
After generating the `TestRequest` class with the relevant validation logic, you can use it within a controller to handle incoming requests, perform validation, and execute any necessary logic (such as file generation).

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Configuration

Ensure that the bundle is registered in `config/bundles.php:

```php
    #[Route('/test', methods: ['POST'])]
    public function test(TestRequest $request): JsonResponse
    {
        //...
        
        // Return a success message after the request is processed
        return new JsonResponse([
            'message' => 'Request exécuted succesfully',
        ]);
    }
```

If validation fails (e.g., missing or invalid file), a response like the following will be returned:

```json

{
   "message": "Validation error",
   "errors": {
      "fichier": "This value is invalid",
      "someProperty": "This value cannot be blank."
   }
}
```

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Larry-Bill ADJE - [@twitter_handle](https://twitter.com/thvvger) - thvger@gmail.com


<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* [Orphé Bobby]()
* [Massoud FATAOU]()

<p align="right">(<a href="#readme-top">back to top</a>)</p>
