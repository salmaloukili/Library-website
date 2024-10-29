<?php


namespace App\Tests\Controller;


use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Feedback;


class FeedbackTest extends WebTestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testFeedbackSubmission(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $crawler = $client->request('GET', '/BookBinder/feedback');

        $form = $crawler->selectButton('Submit Message')->form();

        $form['form[name]'] = 'Test User';
        $form['form[email]'] = 'test@example.com';
        $form['form[phone_number]'] = '1234567890';
        $form['form[message]'] = 'This is a test feedback message.';

        $client->submit($form);

        $feedbackRepository = $entityManager->getRepository(Feedback::class);
        $feedback = $feedbackRepository->findOneBy([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phoneNumber' => '1234567890',
            'message' => 'This is a test feedback message.',
        ]);

        $this->assertNotNull($feedback, 'The feedback was not saved to the database.');

        $this->assertTrue($client->getResponse()->isRedirect('/BookBinder/feedback'));

        $crawler = $client->followRedirect();

        $this->assertStringContainsString(
            'Your feedback has been successfully sent!',
            $client->getResponse()->getContent()
        );
    }


    public function testSaveFeedback(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $feedback = new Feedback();
        $feedback->setName('Test User');
        $feedback->setEmail('test@example.com');
        $feedback->setPhoneNumber('1234567890');
        $feedback->setMessage('This is a test feedback message.');

        $feedbackRepository = $container->get(FeedbackRepository::class);

        $feedbackRepository->save($feedback, true);

        $savedFeedback = $feedbackRepository->findOneBy([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phoneNumber' => '1234567890',
            'message' => 'This is a test feedback message.',
        ]);

        $this->assertEquals('Test User', $savedFeedback->getName());
        $this->assertEquals('test@example.com', $savedFeedback->getEmail());
        $this->assertEquals('1234567890', $savedFeedback->getPhoneNumber());
        $this->assertEquals('This is a test feedback message.', $savedFeedback->getMessage());
    }


    public function testRemoveAllFeedback(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $feedbackRepository = $entityManager->getRepository(Feedback::class);

        $feedback1 = new Feedback();
        $feedback1->setName('Test User');
        $feedback1->setEmail('test@example.com');
        $feedback1->setPhoneNumber('1234567890');
        $feedback1->setMessage('This is a test feedback message.');

        $feedback2 = new Feedback();
        $feedback2->setName('Test User');
        $feedback2->setEmail('test@example.com');
        $feedback2->setPhoneNumber('1234567890');
        $feedback2->setMessage('This is a test feedback message.');

        $feedbackRepository->save($feedback1);
        $feedbackRepository->save($feedback2, true);

        $feedbackRepository->removeAll();

        $remainingFeedback = $feedbackRepository->findAll();
        $this->assertCount(0, $remainingFeedback, 'Not all feedback entities were removed.');
    }

}
