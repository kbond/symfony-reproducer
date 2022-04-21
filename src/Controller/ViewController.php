<?php

namespace App\Controller;

use App\View;
use App\View\Json;
use App\View\Redirect;
use App\View\Serialized;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

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

    #[Route('/no-content', name: 'app_no_content')]
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
