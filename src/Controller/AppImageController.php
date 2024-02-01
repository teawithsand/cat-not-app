<?php

namespace App\Controller;

use App\Entity\AppImage;
use App\Repository\AppImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AppImageType;

class AppImageController extends AbstractController
{
    #[Route('/image/upload', name: 'app_image_upload')]
    public function upload(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $form = $this->createForm(AppImageType::class);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile
             */
            $image = $form->get('data')->getData();

            
            $process = new Process(['python3', '/workspaces/backend/script.py', $image->getRealPath()]);
            $process->run();
            $process->wait();
            $res = $process->getOutput();
            $err = $process->getExitCode();
            dump($err);
            dump($process->getErrorOutput());
            dump($res);
            $res = trim($res);
            $res = (float) $res;

            $data = file_get_contents($image->getRealPath());

            $entity = new AppImage;
            $entity->setOwner($user);
            $entity->setData($data);
            $entity->setMime($image->getMimeType());
            $entity->setCreatedAt(new \DateTimeImmutable);
            $entity->setRating($res);

            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('app_image_info', [
                "id" => $entity->getId(),
            ]);
        }

        return $this->render('image/upload.html.twig', [
            'upload_form' => $form->createView(),
        ]);
    }


    #[Route('/image/info/{id}', name: 'app_image_info')]
    public function info(int $id, AppImageRepository $repo): Response
    {
        $image = $repo->find($id);
        if (!$image)
            throw $this->createNotFoundException();
        
        return $this->render('image/view.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/image/show/{id}', name: 'app_image_show')]
    public function show(int $id, AppImageRepository $repo): Response
    {
        $image = $repo->find($id);
        if (!$image)
            throw $this->createNotFoundException();
        return new Response(stream_get_contents($image->getData()), 200, [
            'Content-Type' => $image->getMime(),
        ]);
    }

    #[Route('/image/list', name: 'app_image_list')]
    public function list(AppImageRepository $imagesRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $user = $this->getUser();
        $images = $imagesRepository->findBy([
            "owner" => $user
        ]);

        return $this->render('image/list.html.twig', [
            'images' => $images,
        ]);
    }
}
