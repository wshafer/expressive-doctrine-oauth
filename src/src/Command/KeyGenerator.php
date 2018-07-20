<?php

declare(strict_types=1);

namespace WShafer\OAuth\Command;

use WShafer\OAuth\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KeyGenerator extends Command
{
    protected $config;

    public function __construct(Config $config) {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName('oauth:generate-keys')
            ->setDescription('Generate encryption keys for Oauth2')
            ->addArgument(
                'privateKeyPath',
                InputArgument::OPTIONAL,
                'Path to install private key',
                $this->config->getPrivateKeyPath()
            )
            ->addArgument(
                'publicKeyPath',
                InputArgument::OPTIONAL,
                'Path to install public key',
                $this->config->getPublicKeyPath()
            )
            ->addArgument(
                'encryptionKeyPath',
                InputArgument::OPTIONAL,
                'Path to install encryption key',
                $this->config->getEncryptionKeyPath()
            );
    }

    /**
     * Executes the current command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $privateKeyPath = $input->getArgument('privateKeyPath');

        if (!$this->isValidPath($privateKeyPath)) {
            throw new \RuntimeException(
                'Unable to create private key. '.$privateKeyPath.' is either can\'t be written to or file already exists'
            );
        }

        $publicKeyPath = $input->getArgument('publicKeyPath');

        if (!$this->isValidPath($publicKeyPath)) {
            throw new \RuntimeException(
                'Unable to create public key. '.$publicKeyPath.' is either can\'t be written to or file already exists'
            );
        }

        $encryptionKeyPath = $input->getArgument('encryptionKeyPath');

        if (!$this->isValidPath($encryptionKeyPath)) {
            throw new \RuntimeException(
                'Unable to create encryption key. '.$encryptionKeyPath.' is either can\'t be written to or file already exists'
            );
        }

        $config = $this->getOpenSSLConfig();
        $privateKey = null;
        $key = openssl_pkey_new($config);
        openssl_pkey_export($key,$privateKey);

        $publicKey = openssl_pkey_get_details($key);
        $encryptionKey = base64_encode(random_bytes(32));

        file_put_contents($privateKeyPath, $privateKey);
        file_put_contents($publicKeyPath, $publicKey['key']);
        file_put_contents($encryptionKeyPath, sprintf("<?php\nreturn '%s';\n", $encryptionKey));

        $output->writeln('Keys generated to:');
        $output->writeln("\t Private Key: ".$privateKeyPath);
        $output->writeln("\t Public Key: ".$publicKeyPath);
        $output->writeln("\t Encryption Key: ".$encryptionKeyPath);
    }

    protected function getOpenSSLConfig()
    {
        return [
            'digest_alg' => 'sha1',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
    }

    protected function isValidPath($path)
    {
        if (empty($path)
            || !is_writable(dirname($path))
            || file_exists($path)
        ) {
            return false;
        }

        return true;
    }
}
