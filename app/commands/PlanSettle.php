<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PlanSettle extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'PlanSettle';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '计划完成提现到余额';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$page = 1;
		$pay = new Pay('HLBPay');
		$pay->settle();
		while (true) {
			$limit = ($page - 1) * 100;
			$take = 100;
			$plans = Plan::where('status', 2)->skip($limit)->take($take)->get();
			if ($plans->count() == 0) {
				$this->info('empty data');
				exit();
			}
			$page++;
			foreach ($plans as $plan) {
				
				try {
					DB::beginTransaction();
					// 生成账单 默认失败 商户带余额
			    	$bill_id = Bill::createBill(array(
						'UserId' => $plan->UserId,
						'money' => $plan->CashDeposit, // 不含手续
						'Type' => 6, // 保证金返回
						'bank_number' => '',
						'OrderNum' => '',
						'feeType' => '',
						'TableId' => $plan->Id,
						'SysFee' => 0,
						'From' => '商户',
						'To' => '余额',
					));
			    	// 直接成功
					Bill::billUpdate($bill_id, 'SUCCESS');
					// 结算到余额
					Plan::where('Id', $plan->Id)->update(array('status'=> 3));
					User::where('UserId', $plan->UserId)->increment('Account', (float)$plan->CashDeposit);
					$this->info('planid: '.$plan->Id.', cash:'. $plan->CashDeposit. ', SUCCESS');
					DB::commit();
				} catch (Exception $e) {
					$this->info('planid: '.$plan->Id.', cash:'. $plan->CashDeposit. ', FAILED');
					DB::rollback();
				}
				
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
