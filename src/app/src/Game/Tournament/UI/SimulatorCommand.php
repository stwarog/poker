<?php declare(strict_types=1);


namespace App\Game\Tournament\UI;


use App\Game\Tournament\Application\TournamentFacade;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class SimulatorCommand extends Command
{
    protected static $defaultName = 'app:sim';
    private TournamentFacade $facade;

    public function __construct(string $name = null, TournamentFacade $facade)
    {
        $this->facade = $facade;
        parent::__construct($name);
    }

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello, lets play the game!');

        $t = $this->facade->create(
            3, 5, 1000, 25, 50, true
        );
        $output->writeln(sprintf('Crated tournament: %s', $t));
        $output->writeln('');

        $helper = $this->getHelper('question');
        $question = new Question('How many users to sign up? ', '2');
        $output->writeln('');

        $players = [];

        $signUps = (int)$helper->ask($input, $output, $question);
        for ($c=0; $c !== $signUps; $c++) {
            $index = $c + 1;
            $id = $this->facade->signUp($t);
            $players[$index] = $id;
            $output->writeln(sprintf('Sign up player: %s with no: %d', $id, $index));
        }

        $output->writeln('');

        $helper = $this->getHelper('question');
        $question = new Question('How many players to join? ', '2');

        $join = (int)$helper->ask($input, $output, $question);
        for ($c=0; $c !== $join; $c++) {
            $index = $c + 1;
            $this->facade->join($t, $players[$index]);
            $output->writeln(sprintf('Joined player: %s', $players[$index]));
        }

        $question = new ConfirmationQuestion('Start tournament ?', true);
        $join = (bool)$helper->ask($input, $output, $question);

        if (!$join) {
            return Command::SUCCESS;
        }

        $this->facade->start($t);

        try {
            $question = new Question('Acting as? ', 1);
            $id = (int)$helper->ask($input, $output, $question);
            $p = $players[$id];

            $question = new ChoiceQuestion(
                'What to do?',
                // choices can also be PHP objects that implement __toString() method
                ['fold', 'call', 'raise', 'allIn'],
                'fold'
            );

            $decision = $helper->ask($input, $output, $question);
            $output->writeln([sprintf('Decision: %s', $decision), '']);

            if ($decision === 'raise') {
                $question = new Question('How much? (100) ', 100);
                $amount = (int)$helper->ask($input, $output, $question);
                $this->facade->raise($t, $p, $amount);
                return Command::SUCCESS;;
            }

            $this->facade->$decision($t, $p);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
