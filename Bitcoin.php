<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Bitcoin class
 *
 * This class enables the communication with the Bitcoin's
 * JSON-RPC API.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Razican
 * @link		https://github.com/Razican/BTCLib
 */
class Bitcoin {

	/**
	 * Url of the JSON-RPC connection
	 *
	 * @var string
	 */
	private $url;

	/**
	 * If the server is connected through SSL
	 *
	 * @var bool
	 */
	private $is_ssl;

	/**
	 * Creates a Bitcoin connection.
	 **/
	public function __construct($config = array())
	{
		if ( ! empty($config) &&
			isset($config['bitcoin_ssl']) && is_bool($config['bitcoin_ssl']) &&
			isset($config['bitcoin_user']) && is_string($config['bitcoin_user']) &&
			isset($config['bitcoin_pass']) && is_string($config['bitcoin_pass']) &&
			isset($config['bitcoin_server']) && is_string($config['bitcoin_server']) &&
			isset($config['bitcoin_port']) && is_int($config['bitcoin_port']) &&
			$config['bitcoin_port'] >= 1024 && $config['bitcoin_port'] <= 65535)
		{
			$this->initialize($config);
		}
		elseif ( ! empty($config))
		{
			log_message('error', 'Invalid configuration provided for the Bitcoin library');
			show_error('Invalid configuration provided for the Bitcoin library', 500);
		}
		else
		{
			$this->url = null;
		}

		log_message('debug', 'Bitcoin library initialized');
	}

	/**
	 * Initialize the connection settings
	 *
	 * Accepts an associative array as input, containing connection settings
	 *
	 * @param	array $config connection settings
	 * @return
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// Set the next_prev_url to the controller if required but not defined
		if ($this->show_next_prev === TRUE && empty($this->next_prev_url))
		{
			$this->next_prev_url = $this->CI->config->site_url($this->CI->router->class.'/'.$this->CI->router->method);
		}
	}

// TODO addmultisigaddress($nrequired, $keys, $account)
// TODO addnode($node, $action)

	/**
	 * Safely copies wallet.dat to destination, which can be a directory or a path with filename.
	 *
	 * @param (string) destination - The destination for the backup.
	 **/
	public function backupwallet($destination)
	{
		return $this->_connect('backupwallet', array(realpath($destination)));
	}

// TODO createmultisig($nrequired, $keys)
// TODO createrawtransaction($tranzaction, $data)
// TODO decoderawtransaction($hexstring)
// TODO dumpprivkey($bitcoinaddress)
// TODO encryptwallet($passphrase)

	/**
	 * Returns the account associated with the given address.
	 *
	 * @param (string) bitcoinaddress -The addres to check
	 **/
	public function getaccount($bitcoinaddress)
	{
		return $this->_connect('getaccount', array((string) $bitcoinaddress));
	}

	/**
	 * Returns the current bitcoin address for receiving payments to this account.
	 *
	 * @param (int) account - The account to check
	 **/
	public function getaccountaddress($account)
	{
		return $this->_connect('getaccountaddress', array((string) (int) $account));
	}

// TODO getaddednodeinfo($dns, $node)
// TODO getaddressesbyaccount($account)

	/**
	 * Returns the balance in the account.
	 *
	 * @param (int) account - The account to check
	 * @param (int) minconf - The minimum confirmations needed for a transaction to be considered as confirmed
	 **/
	public function getbalance($account, $minconf = 1)
	{
		return $this->_connect('getbalance', array((string) (int) $account, (int) $minconf));
	}

// TODO getbestblockhash($index)
// TODO getblock($hash)
// TODO getblockcount()
// TODO getblockhash($index)
// TODO getblocktemplate($params)

	/**
	 * Returns the number of connections to other nodes.
	 *
	 * @return (int) The number of connections
	 **/
	public function getconnectioncount()
	{
		return $this->_connect('getconnectioncount');
	}

// TODO getdifficulty()
// TODO getgenerate()
// TODO gethashespersec()

	/**
	 * Returns an object containing various state info.
	 **/
	public function getinfo()
	{
		return $this->_connect('getinfo');
	}

// TODO getmininginfo()

	/**
	 * Returns a new bitcoin address for receiving payments.
	 * It is added to the address book so payments received with the address will be credited to the account.
	 *
	 * @param (int) account - The account for the new address
	 **/
	public function getnewaddress($account)
	{
		return $this->_connect('getnewaddress', array((string) (int) $account));
	}

// TODO getpeerinfo()
// TODO getrawchangeaddress($account)
// TODO getrawmempool()
// TODO getrawtransaction($txid, $verbose)

	/**
	 * Returns the total amount received by the account in transactions with a minimum confirmations.
	 *
	 * @param (int) account - The account to check
	 * @param (int) minconf - The minimum confirmations needed for a transaction to be considered as confirmed
	 **/
	public function getreceivedbyaccount($account, $minconf = 6)
	{
		return $this->_connect('getreceivedbyaccount', array((string) (int) $account, (int) $minconf));
	}

// TODO getreceivedbyaddress($bitcoinaddress, $minconf)

	/**
	 * Returns an object about the given transaction containing:
	 *		amount - total amount of the transaction
	 *		confirmations - number of confirmations of the transaction
	 *		txid - the transaction ID
	 *		time - time the transaction occurred
	 *		details - An array of objects containing:
	 *			account, address, category, amount, fee
	 *
	 * @param (string) txid - The account to check
	 **/
	public function gettransaction($txid)
	{
		return $this->_connect('gettransaction', array((string) $txid));
	}

// TODO gettxout($txid, $n, $includemempool)
// TODO gettxoutsetinfo()
// TODO getwork($data)
// TODO help($command)
// TODO impòrtprivkey($bitcoinprivkey, $label, $rescan)
// TODO keypoolrefill()

	/**
	 * Returns Object that has account names as keys, account balances as values.
	 *
	 * @param (int) minconf - The minimum confirmations needed for a transaction to be considered as confirmed
	 **/
	public function listaccounts($minconf = 6)
	{
		return $this->_connect('listaccounts', array((int) $minconf));
	}

// TODO listaddressgroupings()

	/**
	 * Returns an array of objects containing:
	 *		account - the account of the receiving addresses
	 *		amount - total amount received by addresses with this account
	 *		confirmations - number of confirmations of the most recent transaction included
	 *
	 * @param (int) minconf - The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param (bool) includeempty - Wether to include account with no transactions
	 **/
	public function listreceivedbyaccount($minconf = 1, $includeempty = FALSE)
	{
		return $this->_connect('listreceivedbyaccount', array((int) $minconf, (bool) $includeempty));
	}

	/**
	 * Returns an array of objects containing:
	 *		address - receiving address
	 *		account - the account of the receiving addresses
	 *		amount - total amount received by addresses with this account
	 *		confirmations - number of confirmations of the most recent transaction included
	 *
	 * @param (int) minconf - The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param (bool) includeempty - Wether to include account with no transactions
	 **/
	public function listreceivedbyaddress($minconf = 1, $includeempty = FALSE)
	{
		return $this->_connect('listreceivedbyaddress', array((int) $minconf, (bool) $includeempty));
	}

//TODO ^
// TODO listsinceblock($blockhash, $target-confirmations)

	/**
	 * Returns the most recent transactions skipping the first given transactions for the given account.
	 *
	 * @param string $account The account to check
	 * @param int $count The number of transactions to return
	 * @param int $from The number of transactions to skip
	 * @return array|object|null The list of transactions, or the error object, or NULL if
	 *								$count or $from were not int
	 **/
	public function listtransactions($account, $count = 10, $from = 0)
	{
		if ( ! is_int($count) OR ! is_int($from))
		{
			log_message('error', 'Bad data received for $count or $from at bitcoin->listtransactions()');
			return NULL;
		}

		$result =  $this->_connect('listtransactions', array((string) $account, $count, $from));
		if ( ! is_null($error = $this->_get_error($result)))
		{
			return $error;
		}

		return $result; //TODO check
	}

	/**
	 * Returns array of unspent transaction inputs in the wallet.
	 *
	 * @param int $minconf The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param int $maxconf The maximum confirmations for a transaction to be showed
	 * @return array|object|null The unexpent transactions, or the error object, or NULL if
	 *								$minconf or $maxconf were not int
	 **/
	public function listunspent($minconf = 1, $maxconf = 999999)
	{
		if ( ! is_int($minconf) OR ! is_int($maxconf))
		{
			log_message('error', 'Bad data received for $minconf or $maxconf at bitcoin->listunspent()');
			return NULL;
		}

		$result = $this->_connect('listunspent', array($minconf, $maxconf));
		if ( ! is_null($error = $this->_get_error($result)))
		{
			return $error;
		}

		return json_decode($result); //TODO test
	}

// TODO listlockunspent()
// TODO lockunspent($unlock, $array-of-objects)

	/**
	 * Move from one account in your wallet to another. It won't use Bitcoin network, and thus,
	 * whon't cost any fee.
	 *
	 * @param string $fromaccount The account from which to transfer funds
	 * @param string $toaccount The account to which send funds
	 * @param int $amount The amount to send
	 * @param int $minconf The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param string $comment The comment for the move
	 * @return bool|object|null if the move was successful, or the error object or NULL
	 *								if $fromaccount or $toaccount or $amount or $minconf were not int
	 **/
	public function move($fromaccount, $toaccount, $amount, $minconf = 1, $comment = '')
	{
		if ( ! is_int($amount) OR ! is_int($minconf))
		{
			log_message('error', 'Bad data received for $amount or $minconf at bitcoin->move()');
			return NULL;
		}

		$result = $this->_connect('move', array((string) $fromaccount, (string) $toaccount,
												$this->amount_to_JSON($amount), $minconf, (string) $comment));
		if ( ! is_null($error = $this->_get_error($result)))
		{
			return $error;
		}

		return (bool) $result;
	}

	/**
	 * Will send the given amount to the given address, ensuring the account has a valid balance
	 * using given confirmations.
	 *
	 * @param string $fromaccount The account from which to transfer funds
	 * @param string $tobitcoinaddress The Bitcoin address for receiving the funds
	 * @param int $amount The amount to send
	 * @param int $minconf The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param string $comment The comment for the sending transaction
	 * @param string $comment_to The comment for the arriving transaction
	 * @return string|object|null The transaction ID, or the error object or NULL
	 *								if $fromaccount or $amount or $minconf were not int
	 **/
	public function sendfrom($fromaccount, $tobitcoinaddress, $amount, $minconf = 1, $comment = '', $comment_to = '')
	{
		if ( ! is_int($amount) OR ! is_int($minconf))
		{
			log_message('error', 'Bad data received for $amount or $minconf at bitcoin->sendfrom()');
			return NULL;
		}

		$result = $this->_connect('sendfrom', array((string) $fromaccount, (string) $tobitcoinaddress, $this->amount_to_JSON($amount),
													$minconf, (string) $comment, (string) $comment_to));
		if ( ! is_null($error = $this->_get_error($result)))
		{
			return $error;
		}

		return $result;
	}

	/**
	 * Sends multiple transactions at one time. It will use a send array to
	 * send different amounts to each address.
	 *
	 * @param string $fromaccount The account from which to transfer funds
	 * @param array $send_array The array with the amounts to send,
	 *		in string $address =>  int $amount format
	 * @param int $minconf The minimum confirmations needed for a transaction to be considered as confirmed
	 * @param string $comment The comment for the sending transaction
	 * @return string|object|null The transaction ID, or the error object or NULL
	 *								if $minconf or any $amount were not int
	 **/
	public function sendmany($fromaccount, $send_array, $minconf = 1, $comment = '')
	{
		if ( ! is_int($minconf))
		{
			log_message('error', 'Bad data received for $minconf at bitcoin->sendmany()');
			return NULL;
		}

		foreach ($send_array as $address => $amount)
		{
			if ( ! is_int($amount) OR ! is_string($address))
			{
				log_message('error', 'Bad data received for $send_array at bitcoin->sendmany()');
				return NULL;
			}

			$send_array[$address] = $this->amount_to_JSON($amount);
		}

		$data = array((string) $fromaccount, $send_array, $minconf, (string) $comment);

		$result = $this->_connect('sendmany', $data);
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return $result; // TODO check
	}

// TODO sendrawtransaction($hexstring)

	/**
	 * Sends money to a given address using the default account.
	 *
	 * @param string $bitcoinaddress The address to which to send funds
	 * @param int $amount The amount to send
	 * @param string $comment The comment for the sending transaction
	 * @param string $comment_to The comment for the arriving transaction
	 * @return string|object|null The transaction ID, or the error object
	 *								or NULL if $amount was not an int
	 **/
	public function sendtoaddress($bitcoinaddress, $amount, $comment = '', $comment_to = '')
	{
		if ( ! is_int($amount))
		{
			log_message('error', 'Bad data received for $amount at bitcoin->sendtoaddress()');
			return NULL;
		}

		$data = array((string) $bitcoinaddress, $this->amount_to_JSON($amount), (string) $comment, (string) $comment_to);

		$result = $this->_connect('sendtoaddress', $data);
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return $result;
	}

// TODO setaccount($bitcoinaddress, $account)
// TODO setgenerate($generate, $genproclimit)

	/**
	 * Sets the new fee for transactions.
	 *
	 * @param int $amount The new amount for the fee
	 * @return bool|object If the modification was successful or the
	 *						error object
	 **/
	public function settxfee($amount)
	{
		if ( ! is_int($amount))
		{
			log_message('error', 'Bad data received for $amount at bitcoin->settxfee()');
			return FALSE;
		}

		$result = $this->_connect('settxfee', array($this->amount_to_JSON($amount)));
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return (bool) $result;
	}

	/**
	 * Sign a message with the private key of an address.
	 *
	 * @param string $bitcoinaddress The address to use for signing
	 * @param string $message The message to sign
	 * @return string|object The signed message, or the error object
	 **/
	public function signmessage($bitcoinaddress, $message)
	{
		$result = $this->_connect('signmessage', array((string) $bitcoinaddress, (string) $message));
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return $result;
	}

// TODO signrawtransaction($hexstring, $transaction, $privatekey)
// TODO stop()
// TODO submitblock($hexdata, $optional_params_obj)

	/**
	 * Return information about a given address.
	 *
	 * @param string $bitcoinaddress The address to check
	 * @return object the data of the account, or the error object
	 **/
	public function validateaddress($bitcoinaddress)
	{
		$result = $this->_connect('validateaddress', array((string) $bitcoinaddress));
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return json_decode($result);
	}

	/**
	 * Verify a signed message.
	 *
	 * @param string $bitcoinaddress The address used for signing
	 * @param string $signature The signature resulted from signing
	 * @param string $message The message
	 * @return bool|object Wether the signature has been verified or not
	 *						or the error ocurred
	 **/
	public function verifymessage($bitcoinaddress, $signature, $message)
	{
		$result = $this->_connect('verifymessage', array((string) $bitcoinaddress, (string) $signature, (string) $message));
		if ( ! is_null($error = $this->_get_error($result)))
			return $error;

		return (bool) $result;
	}

	/**
	 * Removes the wallet encryption key from memory, locking the wallet.
	 * After calling this method, you will need to call walletpassphrase again
	 * before being able to call any methods which require the wallet to be unlocked.
	 *
	 * @return null|object If there is an error, the error object
	 **/
	public function walletlock()
	{
		return $this->_get_error($this->_connect('walletlock'));
	}

	/**
	 * Stores the wallet decryption key in memory for a given amount of seconds.
	 *
	 * @param string $passphrase The passphrase of the wallet
	 * @param int $timeout The number of seconds to store the decryption key in memory
	 * @return bool|object TRUE if everithing went OK, FALSE if $timeout wasn't an int and
	 *						error object if there was an error
	 **/
	public function walletpassphrase($passphrase, $timeout)
	{
		if ( ! is_int($timeout))
		{
			log_message('error', 'Bad data received for $timeout at bitcoin->walletpassphrase()');
			return FALSE;
		}

		if (is_null($error = $this->_get_error($this->_connect('walletpassphrase', array((string) $passphrase, $timeout)))))
			return TRUE;

		return $error;
	}

	/**
	 * Changes the wallet passphrase.
	 *
	 * @param string $oldpassphrase The old passphrase
	 * @param string $newpassphrase The new passphrase to set
	 * @return null|object If there is an error, the error object
	 **/
	public function walletpassphrasechange($oldpassphrase, $newpassphrase)
	{
		return $this->_get_error($this->_connect('walletpassphrasechange', array((string) $oldpassphrase, (string) $newpassphrase)));
	}

	/**
	 * Returns the integer value of the 64 bit double precision number in the JSON-RPC request.
	 *
	 * @param double $value The BTC value to be converted
	 * @return int|null the integer for use with Bitcoin, NULL if $value wasn't numeric
	 * @link https://en.bitcoin.it/wiki/Proper_Money_Handling_(JSON-RPC)
	 **/
	public function JSON_to_amount($value)
	{
		if ( ! is_numeric($value))
		{
			log_message('error', 'Bad data received for $value at bitcoin->JSON_to_amount()');
			return NULL;
		}

		return (int) round($value * 1E+8);
	}

	/**
	 * Returns the 64 bit double precision number for the JSON-RPC request of the integer.
	 *
	 * @param int $value The BTC value to be converted
	 * @return double|null the double to use with the JSON-RPC API, NULL if $value wasn't int
	 * @link https://en.bitcoin.it/wiki/Proper_Money_Handling_(JSON-RPC)
	 **/
	public function amount_to_JSON($value)
	{
		if ( ! is_int($value))
		{
			log_message('error', 'Bad data received for $value at bitcoin->amount_to_JSON()');
			return NULL;
		}

		return (double) round($value * 1E-8, 8);
	}

	/**
	 * Checks if the given error code is a standard JSON-RPC error
	 *
	 * @param int $error_code The error code
	 * @return bool|null if the code is a JSON-RPC error, NULL if $error_code wasn't int
	 **/
	private function _is_rpc_error($error_code)
	{
		if ( ! is_int($error_code))
		{
			log_message('error', 'Bad data received for $error_code at bitcoin->_is_rpc_error()');
			return NULL;
		}

		return in_array($error_code, array(-32600, -32601, -32602, -32603, -32700);
	}

	/**
	 * Checks if the given error code is a Bitcoin general error
	 *
	 * @param int $error_code The error code
	 * @return bool|null if the code is a general error, NULL if $error_code wasn't int
	 **/
	private function _is_general_error($error_code)
	{
		if ( ! is_int($error_code))
		{
			log_message('error', 'Bad data received for $error_code at bitcoin->_is_general_error()');
			return NULL;
		}

		return in_array($error_code, array(-1, -2, -3, -5, -7, -8, -20, -22);
	}

	/**
	 * Checks if the given error code is a Bitcoin wallet error
	 *
	 * @param int $error_code The error code
	 * @return bool|null if the code is a wallet error, NULL if $error_code wasn't int
	 **/
	private function _is_wallet_error($error_code)
	{
		if ( ! is_int($error_code))
		{
			log_message('error', 'Bad data received for $error_code at bitcoin->_is_wallet_error()');
			return NULL;
		}

		return in_array($error_code, array(-4, -6, -11, -12, -13, -14, -15, -16, -17));
	}

	/**
	 * Checks if the given error code is a Bitcoin P2P
	 *
	 * @param int $error_code The error code
	 * @return bool|null if the code is a P2P error, NULL if $error_code wasn't int
	 **/
	private function _is_p2p_error($error_code)
	{
		if ( ! is_int($error_code))
		{
			log_message('error', 'Bad data received for $error_code at bitcoin->_is_p2p_error()');
			return NULL;
		}

		return in_array($error_code, array(-9, -10, -23, -24));
	}

	/**
	 * Gets the error from the request.
	 *
	 * @param string $error the server's response
	 * @return object|null the error, NULL if there was no error
	 **/
	private function _get_error($error)
	{
		if ( ! empty($error) && strstr($error, 'error: '))
		{
			$result = json_decode(substr($error, 7));
			if ($this->_is_rpc_error($result->code))
			{
				log_message('error', 'Bitcoin RPC error: Code -> '.$result->code.' Message -> '.$result->message);
			}
			elseif ($this->_is_general_error($result->code))
			{
				log_message('error', 'Bitcoin error: Code -> '.$result->code.' Message -> '.$result->message);
			}
			elseif ($this->_is_wallet_error($result->code))
			{
				log_message('error', 'Bitcoin wallet error: Code -> '.$result->code.' Message -> '.$result->message);
			}
			elseif ($this->_is_p2p_error($result->code))
			{
				log_message('error', 'Bitcoin P2P error: Code -> '.$result->code.' Message -> '.$result->message);
			}

			return $result;
		}

		return NULL;
	}

	/**
	 * Creates the connection.
	 * Uses code from JSON-RPC PHP client.
	 *
	 * @param string $method the method to use in the JSON-RPC connection
	 * @param array $params the params to pass in the JSON-RPC connection
	 * @return mixed The result of the request
	 * @link http://jsonrpcphp.org/
	 **/
	private function _connect($method, $params = array())
	{
		if (is_null($this->url))
		{
			log_message('error', 'No configuration supplied for the Bircoin library.');
			show_error('No configuration supplied for the Bircoin library.', 500);
		}

		$id = mt_rand(1, 1000000);

		// prepares the request
		$request	= array(
						'method' => $method,
						'params' => $params,
						'id' => $id
						);
		$request	= json_encode($request);

		// performs the HTTP POST
		$opts		= array(($this->is_ssl ? 'https' : 'http') => array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/json',
						'content' => $request
						));
		$context  = stream_context_create($opts);

		if ($fp = fopen($this->url, 'r', FALSE, $context))
		{
			$response = '';
			while ($row = fgets($fp))
			{
				$response.= trim($row)."\n";
			}

			$response = json_decode($response, TRUE);
		}
		else
		{
			log_message('error', 'Unable to connect to '.$this->url);
		}

		// final checks and return
		if ($response['id'] != $id)
		{
			log_message('error', 'Incorrect response id (request id: '.$id.', response id: '.$response['id'].')');
		}

		if ( ! is_null($response['error']))
		{
			log_message('error', 'Request error: '.$response['error']);
		}

		return $response['result'];
	}
}


/* End of file Bitcoin.php */
/* Location: ./application/libraries/Bitcoin.php */