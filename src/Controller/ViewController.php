<?php

namespace App\Controller;

use App\View;
use App\View\Json;
use App\View\Redirect;
use App\View\Serialized;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ViewController
{
    #[Route('/template')]
    public function template(): View
    {
        return View::template('some/template.html.twig', [
            'key' => 'value',
            'form' => $form, // form views are auto created and status set to 422 if submitted & invalid
        ]);

        // every view object has access to the following api:
        return View::template('some/template.html.twig')
            ->withStatus(201)
            ->withHeader('header1', 'value')
            ->withHeader('header2', 'value')
            ->withResponse(function(Response $response) {
                $response->setPublic(); // do any response manipulation
            })
            // future: cache options ie:
            ->withMaxAge(3600)
            ->withSharedMaxAge(3600)
        ;
    }

    #[Route('/redirect')]
    public function redirect(): View
    {
        return View::redirectTo('/path');

        return View::redirectToRoute('foo', ['key' => 'value']);

        // use the Redirect object to be more explicit
        return Redirect::to('/path');

        return Redirect::toRoute('foo', ['key' => 'value']);

        // add flashes
        return Redirect::toRoute('app_homepage', ['foo' => 'bar'])
            ->withError('some error')
            ->withInfo('some info')
        ;
    }

    #[Route('/file')]
    public function file(): View
    {
        return View::file('/path/to/file.jpg');

        // as attachment
        return View::file('/path/to/file.jpg')
            ->asAttachment()
        ;

        // as attachment with custom filename
        return View::file('/path/to/file.jpg')
            ->asAttachment('some-name.jpg')
        ;

        // delete after sending
        return View::file('/path/to/file.jpg')
            ->delete()
        ;

        // explicit helpers
        return View\File::attachment('/path/to/file.jpg');
        return View\File::inline('/path/to/file.jpg');
        return View\File::attachment('/path/to/file.jpg', 'some-name.jpg');
    }

    #[Route('/no-content')]
    public function noContent(): View
    {
        return View::noContent(); // sets status to 204
    }

    #[Route('/json')]
    public function json(): View
    {
        return View::json(['foo' => 'bar']);

        // uses serializer if available and can pass serialization context
        return View::json(['foo' => 'bar'], ['some', 'context']);

        // add callback
        return View::json(['foo' => 'bar'])
            ->withCallback('some_callback()')
        ;

        // explicit helpers
        return Json::ok('data');
        return Json::created('data'); // sets status to 201
        return Json::accepted('data'); // sets status to 202
        return Json::unprocessable('data'); // sets status to 422
    }

    #[Route('/serialize')]
    public function serialize(): View
    {
        // guesses encoding format from request and sets correct headers

        return View::serialize(['date' => new \DateTime()]);

        // with serialization context
        return View::serialize(['date' => new \DateTime()], ['some' => 'context']);

        // explicit helpers
        return Serialized::ok('data');
        return Serialized::created('data'); // sets status to 201
        return Serialized::accepted('data'); // sets status to 202
        return Serialized::unprocessable('data'); // sets status to 422
    }
}
