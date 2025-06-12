<?php

namespace App\Controller;

use App\Entity\CvUpload;
use App\Entity\Education;
use App\Entity\Skill;
use App\Entity\User;
use App\Entity\WorkExperience;
use App\Form\CvUploadFormType;
use App\Form\EducationFormType;
use App\Form\SkillFormType;
use App\Form\WorkExperienceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/profile/manage')]
#[IsGranted('ROLE_USER')]
class ProfileManagementController extends AbstractController
{
    // Education Management
    #[Route('/education/add', name: 'app_profile_education_add', methods: ['GET', 'POST'])]
    public function addEducation(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $education = new Education();
        $education->setUser($user);
        
        $form = $this->createForm(EducationFormType::class, $education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($education);
            $entityManager->flush();

            $this->addFlash('success', 'Education added successfully!');

            // Check if user wants to add another
            if ($request->request->has('save_and_add_another')) {
                return $this->redirectToRoute('app_profile_education_add');
            }

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/education_form.html.twig', [
            'form' => $form,
            'title' => 'Add Education',
        ]);
    }

    #[Route('/education/{id}/edit', name: 'app_profile_education_edit', methods: ['GET', 'POST'])]
    public function editEducation(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $education = $entityManager->getRepository(Education::class)->find($id);
        
        if (!$education || $education->getUser() !== $user) {
            throw $this->createNotFoundException('Education not found.');
        }

        $form = $this->createForm(EducationFormType::class, $education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Education updated successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/education_form.html.twig', [
            'form' => $form,
            'title' => 'Edit Education',
            'education' => $education,
        ]);
    }

    #[Route('/education/{id}/delete', name: 'app_profile_education_delete', methods: ['POST'])]
    public function deleteEducation(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $education = $entityManager->getRepository(Education::class)->find($id);
        
        if (!$education || $education->getUser() !== $user) {
            throw $this->createNotFoundException('Education not found.');
        }

        $entityManager->remove($education);
        $entityManager->flush();

        $this->addFlash('success', 'Education deleted successfully!');
        return $this->redirectToRoute('app_user_profile');
    }

    // Work Experience Management
    #[Route('/experience/add', name: 'app_profile_experience_add', methods: ['GET', 'POST'])]
    public function addWorkExperience(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $experience = new WorkExperience();
        $experience->setUser($user);
        
        $form = $this->createForm(WorkExperienceFormType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($experience);
            $entityManager->flush();

            $this->addFlash('success', 'Work experience added successfully!');

            // Check if user wants to add another
            if ($request->request->has('save_and_add_another')) {
                return $this->redirectToRoute('app_profile_experience_add');
            }

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/experience_form.html.twig', [
            'form' => $form,
            'title' => 'Add Work Experience',
        ]);
    }

    #[Route('/experience/{id}/edit', name: 'app_profile_experience_edit', methods: ['GET', 'POST'])]
    public function editWorkExperience(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $experience = $entityManager->getRepository(WorkExperience::class)->find($id);
        
        if (!$experience || $experience->getUser() !== $user) {
            throw $this->createNotFoundException('Work experience not found.');
        }

        $form = $this->createForm(WorkExperienceFormType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Work experience updated successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/experience_form.html.twig', [
            'form' => $form,
            'title' => 'Edit Work Experience',
            'experience' => $experience,
        ]);
    }

    #[Route('/experience/{id}/delete', name: 'app_profile_experience_delete', methods: ['POST'])]
    public function deleteWorkExperience(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $experience = $entityManager->getRepository(WorkExperience::class)->find($id);
        
        if (!$experience || $experience->getUser() !== $user) {
            throw $this->createNotFoundException('Work experience not found.');
        }

        $entityManager->remove($experience);
        $entityManager->flush();

        $this->addFlash('success', 'Work experience deleted successfully!');
        return $this->redirectToRoute('app_user_profile');
    }

    // Skill Management
    #[Route('/skill/add', name: 'app_profile_skill_add', methods: ['GET', 'POST'])]
    public function addSkill(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $skill = new Skill();
        $skill->setUser($user);
        
        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($skill);
            $entityManager->flush();

            $this->addFlash('success', 'Skill added successfully!');

            // Check if user wants to add another
            if ($request->request->has('save_and_add_another')) {
                return $this->redirectToRoute('app_profile_skill_add');
            }

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/skill_form.html.twig', [
            'form' => $form,
            'title' => 'Add Skill',
        ]);
    }

    #[Route('/skill/{id}/edit', name: 'app_profile_skill_edit', methods: ['GET', 'POST'])]
    public function editSkill(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $skill = $entityManager->getRepository(Skill::class)->find($id);
        
        if (!$skill || $skill->getUser() !== $user) {
            throw $this->createNotFoundException('Skill not found.');
        }

        $form = $this->createForm(SkillFormType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Skill updated successfully!');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('profile_management/skill_form.html.twig', [
            'form' => $form,
            'title' => 'Edit Skill',
            'skill' => $skill,
        ]);
    }

    #[Route('/skill/{id}/delete', name: 'app_profile_skill_delete', methods: ['POST'])]
    public function deleteSkill(int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $skill = $entityManager->getRepository(Skill::class)->find($id);
        
        if (!$skill || $skill->getUser() !== $user) {
            throw $this->createNotFoundException('Skill not found.');
        }

        $entityManager->remove($skill);
        $entityManager->flush();

        $this->addFlash('success', 'Skill deleted successfully!');
        return $this->redirectToRoute('app_user_profile');
    }

    // CV Upload Management
    #[Route('/cv/upload', name: 'app_profile_cv_upload', methods: ['GET', 'POST'])]
    public function uploadCv(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CvUploadFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cvFile = $form->get('cvFile')->getData();
            $description = $form->get('description')->getData();

            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/cv',
                        $newFilename
                    );

                    // Remove old CV if exists
                    if ($user->getCvUpload()) {
                        $oldCvPath = $this->getParameter('kernel.project_dir').'/public/uploads/cv/'.$user->getCvUpload()->getFileName();
                        if (file_exists($oldCvPath)) {
                            unlink($oldCvPath);
                        }
                        $entityManager->remove($user->getCvUpload());
                    }

                    // Create new CV upload record
                    $cvUpload = new CvUpload();
                    $cvUpload->setUser($user);
                    $cvUpload->setOriginalFileName($cvFile->getClientOriginalName());
                    $cvUpload->setFileName($newFilename);
                    $cvUpload->setFileExtension($cvFile->guessExtension());
                    $cvUpload->setFileSize($cvFile->getSize());
                    if ($description) {
                        $cvUpload->setDescription($description);
                    }

                    $entityManager->persist($cvUpload);
                    $entityManager->flush();

                    $this->addFlash('success', 'CV uploaded successfully!');
                    return $this->redirectToRoute('app_user_profile');

                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your CV. Please try again.');
                }
            }
        }

        return $this->render('profile_management/cv_upload.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/cv/download', name: 'app_profile_cv_download', methods: ['GET'])]
    public function downloadCv(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $cvUpload = $user->getCvUpload();
        if (!$cvUpload) {
            throw $this->createNotFoundException('No CV found.');
        }

        $filePath = $this->getParameter('kernel.project_dir').'/public/uploads/cv/'.$cvUpload->getFileName();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('CV file not found.');
        }

        return $this->file($filePath, $cvUpload->getOriginalFileName());
    }

    #[Route('/cv/delete', name: 'app_profile_cv_delete', methods: ['POST'])]
    public function deleteCv(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $cvUpload = $user->getCvUpload();
        if (!$cvUpload) {
            throw $this->createNotFoundException('No CV found.');
        }

        // Delete file from filesystem
        $filePath = $this->getParameter('kernel.project_dir').'/public/uploads/cv/'.$cvUpload->getFileName();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Remove from database
        $entityManager->remove($cvUpload);
        $entityManager->flush();

        $this->addFlash('success', 'CV deleted successfully!');
        return $this->redirectToRoute('app_user_profile');
    }
}
