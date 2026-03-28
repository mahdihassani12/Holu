<?php
session_start();
if(isset($_SESSION['holu_users_id']) AND isset($_SESSION['holu_username'])){

	date_default_timezone_set('Asia/Kabul');
	
	include("_db.php");
	include("lib/Zebra_Pagination.php");

	// instantiate the pagination object
  $pagination = new Zebra_Pagination();
  // show records in reverse order
  $pagination->reverse(false);

	//general_attributes
	$holu_system_name = "Holu";

	$holu_date = date("Y-m-d");

	$holu_time = date("H:i:s");

	$holu_users_id = $_SESSION['holu_users_id'];

	$holu_currencies = ['AFN', 'USD', 'IRT'];

	$holu_transaction_types = ['Income', 'Expense', 'Exchange', 'Purchase', 'Transfer'];

	$holu_provinces = ['Kabul', 'Herat', 'Mazar_Sharif', 'Badghis', 'Jalaal_Abad'];

	$holu_additional_informations = [
		'Customer Name', 
		'Customer ID', 
		'Package', 
		'Start Date', 
		'End Date', 
		'Equipment',
		'Other Services'
	];

	$holu_filtering_data = "";
	$holu_filtering_array = array();

	$holu_num_record_per_page = 10;
	$holu_from = 0;
	$holu_to = 0;
	$holu_count = 0;

	//delta sib connenction sub_category_id
	$dsc_sub_categories_id = 6;



	$holu_portions = array(
		array(
			"type" => "sidebar",
			"url" => "home.php",
			"label" => "Home",
			"icon" => 'fa fa-home',
			"location" => "index/",
			"path" => "system_accessibility/home/",
			"subs" => array(
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Default",
					"icon" => '',
					"location" => "default/",
					"path" => "system_accessibility/home/default/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Closing Balance Widget",
					"icon" => '',
					"location" => "closing_balance/",
					"path" => "system_accessibility/home/closing_balance/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "10 Highest Expenses",
					"icon" => '',
					"location" => "ten_highest_expenses/",
					"path" => "system_accessibility/home/ten_highest_expenses/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Num Transaction",
					"icon" => '',
					"location" => "num_transaction/",
					"path" => "system_accessibility/home/num_transaction/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Monthly Income",
					"icon" => '',
					"location" => "monthly_income/",
					"path" => "system_accessibility/home/monthly_income/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Monthly Expense",
					"icon" => '',
					"location" => "monthly_expense/",
					"path" => "system_accessibility/home/monthly_expense/",
					"subs" => array(),
				),
				array(
					"type" => "operation",
					"url" => "",
					"label" => "Monthly Purchase",
					"icon" => '',
					"location" => "monthly_purchase/",
					"path" => "system_accessibility/home/monthly_purchase/",
					"subs" => array(),
				),
			),
		),
		array(
			"type" => "sidebar",
			"url" => "",
			"label" => "Transaction",
			"icon" => 'fas fa-exchange-alt',
			"location" => "transaction/",
			"path" => "system_accessibility/transaction/",
			"subs" => array(
				array(
					"type" => "sidebar",
					"url" => "list_income.php",
					"label" => "Income",
					"icon" => '',
					"location" => "income/",
					"path" => "system_accessibility/transaction/income/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Income",
							"icon" => '',
							"location" => "view_income/",
							"path" => "system_accessibility/transaction/income/view_income",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Income",
							"icon" => '',
							"location" => "add_income/",
							"path" => "system_accessibility/transaction/income/add_income",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Income",
							"icon" => '',
							"location" => "edit_income/",
							"path" => "system_accessibility/transaction/income/edit_income",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Income",
							"icon" => '',
							"location" => "delete_income/",
							"path" => "system_accessibility/transaction/income/delete_income",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Print Receipt",
							"icon" => '',
							"location" => "print_receipt/",
							"path" => "system_accessibility/transaction/income/print_receipt/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Attachment",
							"icon" => '',
							"location" => "add_attachment/",
							"path" => "system_accessibility/transaction/income/add_attachment/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => '',
							"location" => "edit_attachment/",
							"path" => "system_accessibility/transaction/income/edit_attachment",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/transaction/income/filter_table",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/transaction/income/tms_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/transaction/income/qb_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/transaction/income/sib_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/transaction/income/ad_markup",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_expense.php",
					"label" => "Expense",
					"icon" => '',
					"location" => "expense/",
					"path" => "system_accessibility/transaction/expense/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Expense",
							"icon" => '',
							"location" => "view_expense/",
							"path" => "system_accessibility/transaction/expense/view_expense",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Expense",
							"icon" => '',
							"location" => "add_expense/",
							"path" => "system_accessibility/transaction/expense/add_expense",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Expense",
							"icon" => '',
							"location" => "edit_expense/",
							"path" => "system_accessibility/transaction/expense/edit_expense",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Expense",
							"icon" => '',
							"location" => "delete_expense/",
							"path" => "system_accessibility/transaction/expense/delete_expense",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Print Voucher",
							"icon" => '',
							"location" => "print_voucher/",
							"path" => "system_accessibility/transaction/expense/print_voucher/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Attachment",
							"icon" => '',
							"location" => "add_attachment/",
							"path" => "system_accessibility/transaction/expense/add_attachment/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => '',
							"location" => "edit_attachment/",
							"path" => "system_accessibility/transaction/expense/edit_attachment",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/transaction/expense/filter_table",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/transaction/expense/tms_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/transaction/expense/qb_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/transaction/expense/sib_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/transaction/expense/ad_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Check Number",
							"icon" => '',
							"location" => "edit_check_number/",
							"path" => "system_accessibility/transaction/expense/edit_check_number/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_exchange.php",
					"label" => "Exchange",
					"icon" => '',
					"location" => "exchange/",
					"path" => "system_accessibility/transaction/exchange/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Exchange",
							"icon" => '',
							"location" => "view_exchange/",
							"path" => "system_accessibility/transaction/exchange/view_exchange",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Exchange",
							"icon" => '',
							"location" => "add_exchange/",
							"path" => "system_accessibility/transaction/exchange/add_exchange",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Exchange",
							"icon" => '',
							"location" => "edit_exchange/",
							"path" => "system_accessibility/transaction/exchange/edit_exchange",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Exchange",
							"icon" => '',
							"location" => "delete_exchange/",
							"path" => "system_accessibility/transaction/exchange/delete_exchange",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/transaction/exchange/filter_table",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/transaction/exchange/tms_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/transaction/exchange/qb_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/transaction/exchange/sib_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/transaction/exchange/ad_markup",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_transfer.php",
					"label" => "Transfer",
					"icon" => '',
					"location" => "transfer/",
					"path" => "system_accessibility/transaction/transfer/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Transfer",
							"icon" => '',
							"location" => "view_transfer/",
							"path" => "system_accessibility/transaction/transfer/view_transfer",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Transfer",
							"icon" => '',
							"location" => "add_transfer/",
							"path" => "system_accessibility/transaction/transfer/add_transfer",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Transfer",
							"icon" => '',
							"location" => "edit_transfer/",
							"path" => "system_accessibility/transaction/transfer/edit_transfer",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Transfer",
							"icon" => '',
							"location" => "delete_transfer/",
							"path" => "system_accessibility/transaction/transfer/delete_transfer",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/transaction/transfer/filter_table",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/transaction/transfer/tms_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/transaction/transfer/qb_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/transaction/transfer/sib_markup",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/transaction/transfer/ad_markup",
							"subs" => array(),
						),
						array( // Added by Mohsen _ 2021-04-04
							"type" => "operation",
							"url" => "",
							"label" => "Print Voucher",
							"icon" => "",
							'location' => "print_vocher/",
							"path" => "system_accessibility/transaction/transfer/print_voucher",
							"subs" => array(),
						),
						array( // Added by Mohsen _ 2021-04-04
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => "",
							'location' => "view_attachment/",
							"path" => "system_accessibility/transaction/transfer/view_attachment",
							"subs" => array(),
						)
					),
				),
			),
		),
		array(
			"type" => "sidebar",
			"url" => "",
			"label" => "Requests",
			"icon" => 'fas fa-question-circle',
			"location" => "request/",
			"path" => "system_accessibility/request/",
			"subs" => array(
				array(
					"type" => "sidebar",
					"url" => "report_cash_reservation.php",
					"label" => "Cash Reservation",
					"icon" => '',
					"location" => "report_cash_reservation/",
					"path" => "system_accessibility/request/report_cash_reservation/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/request/report_cash_reservation/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Approve Cash Reservation",
							"icon" => '',
							"location" => "approve_cash_reservation/",
							"path" => "system_accessibility/request/report_cash_reservation/approve_cash_reservation",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_purchase.php",
					"label" => "Purchase Approve",
					"icon" => '',
					"location" => "report_purchase/",
					"path" => "system_accessibility/request/report_purchase/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/request/report_purchase/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Approve Purchase",
							"icon" => '',
							"location" => "approve_purchase/",
							"path" => "system_accessibility/request/report_purchase/approve_purchase/",
							"subs" => array(),
						),
						array( // Added By Mohsen _ 2021-04-14
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => '',
							"location" => "view_attachment/",
							"path" => "system_accessibility/request/report_purchase/view_attachment",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_purchase_include.php",
					"label" => "Purchase Include",
					"icon" => '',
					"location" => "report_purchase_include/",
					"path" => "system_accessibility/request/report_purchase_include/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/request/report_purchase_include/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "include Purchase",
							"icon" => '',
							"location" => "include_purchase/",
							"path" => "system_accessibility/request/report_purchase_include/include_purchase/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/request/report_purchase_include/filter_table",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => '',
							"location" => "view_attachment/",
							"path" => "system_accessibility/request/report_purchase_include/view_attachment",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_transfer.php",
					"label" => "Transfer",
					"icon" => '',
					"location" => "report_transfer/",
					"path" => "system_accessibility/request/report_transfer/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/request/report_transfer/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Approve Purchase",
							"icon" => '',
							"location" => "approve_transfer/",
							"path" => "system_accessibility/request/report_transfer/approve_transfer/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/request/report_transfer/filter_table",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_tms_income_request.php",
					"label" => "TMS Requests",
					"icon" => '',
					"location" => "report_tms_income_request/",
					"path" => "system_accessibility/request/report_tms_income_request/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/request/report_tms_income_request/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Approve TMS Income Request",
							"icon" => '',
							"location" => "approve_tms_income_request/",
							"path" => "system_accessibility/request/report_tms_income_request/approve_tms_income_request/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Print Receipt",
							"icon" => '',
							"location" => "print_receipt/",
							"path" => "system_accessibility/request/report_tms_income_request/print_receipt/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Filter Table",
							"icon" => '',
							"location" => "filter_table/",
							"path" => "system_accessibility/request/report_tms_income_request/filter_table",
							"subs" => array(),
						),
					),
				),
			),
		),
		array(
			"type" => "sidebar",
			"url" => "",
			"label" => "Reports",
			"icon" => 'fas fa-clipboard',
			"location" => "report/",
			"path" => "system_accessibility/report/",
			"subs" => array(
				array(
					"type" => "sidebar",
					"url" => "report_transaction.php",
					"label" => "Transactions",
					"icon" => '',
					"location" => "report_transaction/",
					"path" => "system_accessibility/report/report_transaction/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Transaction",
							"icon" => '',
							"location" => "view_transaction/",
							"path" => "system_accessibility/report/report_transaction/view_transaction/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/report/report_transaction/tms_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/report/report_transaction/qb_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/report/report_transaction/sib_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/report/report_transaction/ad_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit SIB Number",
							"icon" => '',
							"location" => "edit_sib_number/",
							"path" => "system_accessibility/report/report_transaction/edit_sib_number/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Check Number",
							"icon" => '',
							"location" => "edit_check_nnumber/",
							"path" => "system_accessibility/report/report_transaction/edit_check_number/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Comment",
							"icon" => '',
							"location" => "view_commnet/",
							"path" => "system_accessibility/report/report_transaction/view_commnet/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Attachment",
							"icon" => '',
							"location" => "view_attachment/",
							"path" => "system_accessibility/report/report_transaction/view_attachment/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Transaction",
							"icon" => '',
							"location" => "edit_transaction/",
							"path" => "system_accessibility/report/report_transaction/edit_transaction/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Export Excel",
							"icon" => '',
							"location" => "export_excel/",
							"path" => "system_accessibility/report/report_transaction/export_excel/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Print Receipt",
							"icon" => '',
							"location" => "print_receipt/",
							"path" => "system_accessibility/report/report_transaction/print_receipt/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Print Voucher",
							"icon" => '',
							"location" => "print_voucher/",
							"path" => "system_accessibility/report/report_transaction/print_voucher/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_component.php",
					"label" => "Components",
					"icon" => '',
					"location" => "report_component/",
					"path" => "system_accessibility/report/report_component/",
					"subs" => array(),
				),
				array(
					"type" => "sidebar",
					"url" => "report_accounting.php",
					"label" => "Accounting",
					"icon" => '',
					"location" => "report_accounting/",
					"path" => "system_accessibility/report/report_accounting/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Transaction",
							"icon" => '',
							"location" => "view_transaction/",
							"path" => "system_accessibility/report/report_accounting/view_transaction/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/report/report_accounting/tms_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/report/report_accounting/qb_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/report/report_accounting/sib_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/report/report_accounting/ad_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit SIB Number",
							"icon" => '',
							"location" => "edit_sib_number/",
							"path" => "system_accessibility/report/report_accounting/edit_sib_number/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Accounting Note",
							"icon" => '',
							"location" => "edit_accounting_note/",
							"path" => "system_accessibility/report/report_accounting/edit_accounting_note/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Comment",
							"icon" => '',
							"location" => "view_commnet/",
							"path" => "system_accessibility/report/report_accounting/view_commnet/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Transaction",
							"icon" => '',
							"location" => "edit_transaction/",
							"path" => "system_accessibility/report/report_accounting/edit_transaction/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Reject Transaction",
							"icon" => '',
							"location" => "reject_transaction/",
							"path" => "system_accessibility/report/report_accounting/reject_transaction/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Full Info",
							"icon" => '',
							"location" => "view_full_info/",
							"path" => "system_accessibility/report/report_accounting/view_full_info/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_transaction_edition.php",
					"label" => "Transaction Edition",
					"icon" => '',
					"location" => "report_transaction_edition/",
					"path" => "system_accessibility/report/report_transaction_edition/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/report/report_transaction_edition/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Full Info",
							"icon" => '',
							"location" => "view_full_info/",
							"path" => "system_accessibility/report/report_transaction_edition/view_full_info/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/report/report_transaction_edition/tms_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/report/report_transaction_edition/qb_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/report/report_transaction_edition/sib_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/report/report_transaction_edition/ad_markup/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_transaction_deletion.php",
					"label" => "Transaction Deletion",
					"icon" => '',
					"location" => "report_transaction_deletion/",
					"path" => "system_accessibility/report/report_transaction_deletion/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/report/report_transaction_deletion/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Full Info",
							"icon" => '',
							"location" => "view_full_info/",
							"path" => "system_accessibility/report/report_transaction_deletion/view_full_info/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "TMS Markup",
							"icon" => '',
							"location" => "tms_markup/",
							"path" => "system_accessibility/report/report_transaction_deletion/tms_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "QB Markup",
							"icon" => '',
							"location" => "qb_markup/",
							"path" => "system_accessibility/report/report_transaction_deletion/qb_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "SIB Markup",
							"icon" => '',
							"location" => "sib_markup/",
							"path" => "system_accessibility/report/report_transaction_deletion/sib_markup/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Ad Markup",
							"icon" => '',
							"location" => "ad_markup/",
							"path" => "system_accessibility/report/report_transaction_deletion/ad_markup/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_transaction_print.php",
					"label" => "Transaction Print",
					"icon" => '',
					"location" => "report_transaction_print/",
					"path" => "system_accessibility/report/report_transaction_print/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/report/report_transaction_print/index/",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "View Full Info",
							"icon" => '',
							"location" => "view_full_info/",
							"path" => "system_accessibility/report/report_transaction_print/view_full_info/",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "report_suspicious_transaction.php",
					"label" => "Suspicious Transaction",
					"icon" => '',
					"location" => "report_suspicious_transaction/",
					"path" => "system_accessibility/report/report_suspicious_transaction/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Index",
							"icon" => '',
							"location" => "index/",
							"path" => "system_accessibility/report/report_suspicious_transaction/index/",
							"subs" => array(),
						),
					),
				),
			),
		),
		array(
			"type" => "sidebar",
			"url" => "",
			"label" => "Management",
			"icon" => 'fas fa-vector-square',
			"location" => "management/",
			"path" => "system_accessibility/management/",
			"subs" => array(
				array(
					"type" => "sidebar",
					"url" => "list_category.php",
					"label" => "List of Categories",
					"icon" => '',
					"location" => "list_category/",
					"path" => "system_accessibility/management/list_category/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Category",
							"icon" => '',
							"location" => "add_category/",
							"path" => "system_accessibility/management/list_category/add_category",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Category",
							"icon" => '',
							"location" => "edit_category/",
							"path" => "system_accessibility/management/list_category/edit_category",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Category",
							"icon" => '',
							"location" => "delete_category/",
							"path" => "system_accessibility/management/list_category/delete_category",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_sub_category.php",
					"label" => "List of Sub Categories",
					"icon" => '',
					"location" => "list_sub_category/",
					"path" => "system_accessibility/management/list_sub_category/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Sub Category",
							"icon" => '',
							"location" => "add_sub_category/",
							"path" => "system_accessibility/management/list_sub_category/add_sub_category",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Sub Category",
							"icon" => '',
							"location" => "edit_sub_category/",
							"path" => "system_accessibility/management/list_sub_category/edit_sub_category",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Sub Category",
							"icon" => '',
							"location" => "delete_sub_category/",
							"path" => "system_accessibility/management/list_sub_category/delete_sub_category",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_logistic_cash.php",
					"label" => "List of Logistic Cashes",
					"icon" => '',
					"location" => "list_logistic_cash/",
					"path" => "system_accessibility/management/list_logistic_cash/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add Logistic Cash",
							"icon" => '',
							"location" => "add_logistic_cash/",
							"path" => "system_accessibility/management/list_logistic_cash/add_logistic_cash",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit Logistic Cash",
							"icon" => '',
							"location" => "edit_logistic_cash/",
							"path" => "system_accessibility/management/list_logistic_cash/edit_logistic_cash",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete Logistic Cash",
							"icon" => '',
							"location" => "delete_logistic_cash/",
							"path" => "system_accessibility/management/list_logistic_cash/delete_logistic_cash",
							"subs" => array(),
						),
					),
				),
				array(
					"type" => "sidebar",
					"url" => "list_user.php",
					"label" => "List of Users",
					"icon" => '',
					"location" => "list_user/",
					"path" => "system_accessibility/management/list_user/",
					"subs" => array(
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Add User",
							"icon" => '',
							"location" => "add_user/",
							"path" => "system_accessibility/management/list_user/add_user",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Edit User",
							"icon" => '',
							"location" => "edit_user/",
							"path" => "system_accessibility/management/list_user/edit_user",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Delete User",
							"icon" => '',
							"location" => "delete_user/",
							"path" => "system_accessibility/management/list_user/delete_user",
							"subs" => array(),
						),
						array(
							"type" => "operation",
							"url" => "",
							"label" => "Set Accessibility",
							"icon" => '',
							"location" => "set_accessibility/",
							"path" => "system_accessibility/management/list_user/set_accessibility",
							"subs" => array(),
						),
					),
				),
			),
		),
	);

	function holu_encode($input){
		$output = base64_encode(base64_encode($input));
		return $output;
	}

	function holu_decode($input){
		$output = base64_decode(base64_decode($input));
		return $output;
	}

	//general_functions
	function get_category_option($category_type, $categories_id){
		global $db;
		$category_options = "";
		$query_portion = "";
		if($category_type!="0"){
			$query_portion .= " AND category_type='$category_type' ";
		}

		

		if($categories_id=="0"){
			$category_sq = $db->query("SELECT * FROM `categories` WHERE deleted='0' $query_portion");
			if($category_sq->rowCount()>0){
				$category_options .= '<option selected value="">Select an option...</option>';
				while($category_row = $category_sq->fetch()){
					if(check_access('sub_category_accessibility/'.get_col('categories', 'category_type', 'id', $category_row['id']).'/'.$category_row['id'].'/')==1){
						$category_options .= '<option value="'.$category_row['id'].'">'.$category_row['category_name'].'</option>';
					}
				}
			}
		}else{
			$category_sq = $db->query("SELECT * FROM `categories` WHERE deleted='0' $query_portion");
			if($category_sq->rowCount()>0){
				$category_options .= '<option selected value="">Select an option...</option>';
				while($category_row = $category_sq->fetch()){
					if(check_access('sub_category_accessibility/'.get_col('categories', 'category_type', 'id', $category_row['id']).'/'.$category_row['id'].'/')==1){
						$category_options .= '<option '.(($category_row['id']==$categories_id)?'selected':'').' value="'.$category_row['id'].'">'.$category_row['category_name'].'</option>';
					}
				}
			}
		}
		return $category_options;
	}

	//general_functions
	function get_logistic_cash_option($logistic_cashes_id){
		global $db;
		$logistic_cash_options = "";
		$query_portion = "";

		if($logistic_cashes_id=="0"){
			$logistic_cash_sq = $db->query("SELECT * FROM `logistic_cashes` WHERE deleted='0' $query_portion");
			if($logistic_cash_sq->rowCount()>0){
				$logistic_cash_options .= '<option selected value="">Select an option...</option>';
				while($logistic_cash_row = $logistic_cash_sq->fetch()){
					if(check_access('logistic_cash_accessibility/'.$logistic_cash_row['id'].'/')==1){
						$logistic_cash_options .= '<option value="'.$logistic_cash_row['id'].'">'.$logistic_cash_row['name'].'</option>';
					}
				}
			}
		}else{
			$logistic_cash_sq = $db->query("SELECT * FROM `logistic_cashes` WHERE deleted='0' $query_portion");
			if($logistic_cash_sq->rowCount()>0){
				$logistic_cash_options .= '<option selected value="">Select an option...</option>';
				while($logistic_cash_row = $logistic_cash_sq->fetch()){
					if(check_access('logistic_cash_accessibility/'.$logistic_cash_row['id'].'/')==1){
						$logistic_cash_options .= '<option '.(($logistic_cash_row['id']==$logistic_cashes_id)?'selected':'').' value="'.$logistic_cash_row['id'].'">'.$logistic_cash_row['name'].'</option>';
					}
				}
			}
		}
		return $logistic_cash_options;
	}

	function get_user_option($users_id){
		global $db;
		$user_options = "";

		if(count($users_id)==0){
			$user_sq = $db->query("SELECT * FROM `users` WHERE deleted='0'");
			if($user_sq->rowCount()>0){
				while($user_row = $user_sq->fetch()){
					
					$user_options .= '<option value="'.$user_row['id'].'">'.$user_row['username'].'</option>';
					
				}
			}
		}else{
			$user_sq = $db->query("SELECT * FROM `users` WHERE deleted='0'");
			if($user_sq->rowCount()>0){
				while($user_row = $user_sq->fetch()){
					
					$user_options .= '<option '.((in_array($user_row['id'], $users_id))?'selected':'').' value="'.$user_row['id'].'">'.$user_row['username'].'</option>';
					
				}
			}
		}
		return $user_options;
	}


	function get_employee_option($employees_id){
		global $db;
		$employee_options = "";

		if($employees_id=="0"){
			$employee_sq = $db->query("SELECT * FROM `hr_employees` WHERE deleted='0'");
			if($employee_sq->rowCount()>0){
				$employee_options .= '<option selected hidden value="">Select an option...</option>';
				while($employee_row = $employee_sq->fetch()){
					
					$employee_options .= '<option value="'.$employee_row['id'].'">'.$employee_row['first_name'].' - '.$employee_row['last_name'].' - '.$employee_row['id_number'].'</option>';
					
				}
			}
		}else{
			$employee_sq = $db->query("SELECT * FROM `hr_employees` WHERE deleted='0'");
			if($employee_sq->rowCount()>0){
				while($employee_row = $employee_sq->fetch()){
					
					$employee_options .= '<option '.(($employee_row['id']==$employees_id)?'selected':'').' value="'.$employee_row['id'].'">'.$employee_row['first_name'].' - '.$employee_row['last_name'].' - '.$employee_row['id_number'].'</option>';
					
				}
			}
		}
		return $employee_options;
	}



	function get_currency_option($currency){
		global $holu_currencies;
		$currency_options = "";
		if($currency=="0"){
			foreach ($holu_currencies as $holu_currency) {
				$currency_options .= '<option value="'.$holu_currency.'">'.$holu_currency.'</option>';
			}
		}else{
			foreach ($holu_currencies as $holu_currency) {
				$currency_options .= '<option '.(($holu_currency==$currency)?'selected':'').' value="'.$holu_currency.'">'.$holu_currency.'</option>';
			}
		}
		return $currency_options;
	}

	function get_transaction_type_option($transaction_type){
		global $holu_transaction_types;
		$transaction_type_options = "";
		if($transaction_type=="0"){
			foreach ($holu_transaction_types as $holu_transaction_type) {
				$transaction_type_options .= '<option value="'.$holu_transaction_type.'">'.$holu_transaction_type.'</option>';
			}
		}else{
			foreach ($holu_transaction_types as $holu_transaction_type) {
				$transaction_type_options .= '<option '.(($holu_transaction_type==$transaction_type)?'selected':'').' value="'.$holu_transaction_type.'">'.$holu_transaction_type.'</option>';
			}
		}
		return $transaction_type_options;
	}

	function get_province_option($province){
		global $holu_provinces;
		global $db;
		global $holu_users_id;
		$province_options = "";
		if($province=="0"){
			foreach ($holu_provinces as $holu_province) {
				$access_point = 'province_accessibility/'.$holu_province.'/';
				if(check_access($access_point)==1){
					$province_options .= '<option value="'.$holu_province.'">'.$holu_province.'</option>';
				}
			}
		}else{
			foreach ($holu_provinces as $holu_province) {
				$access_point = 'province_accessibility/'.$holu_province.'/';
				if(check_access($access_point)==1){
					$province_options .= '<option '.(($holu_province==$province)?'selected':'').' value="'.$holu_province.'">'.$holu_province.'</option>';
				}
			}
		}
		return $province_options;
	}

	function get_all_province_option($province){
		global $holu_provinces;
		global $db;
		global $holu_users_id;
		$province_options = "";
		if($province=="0"){
			foreach ($holu_provinces as $holu_province) {
				$province_options .= '<option value="'.$holu_province.'">'.$holu_province.'</option>';
			}
		}else{
			foreach ($holu_provinces as $holu_province) {
				$province_options .= '<option '.(($holu_province==$province)?'selected':'').' value="'.$holu_province.'">'.$holu_province.'</option>';
			}
		}
		return $province_options;
	}

	function get_additional_information_option($additional_information){
		global $holu_additional_informations;
		$additional_information_options = "";
		if($additional_information=="0"){
			$additional_information_options = '<option selected hidden value="">Select an option</option>';
			foreach ($holu_additional_informations as $holu_additional_information) {
				
				$additional_information_options .= '<option value="'.$holu_additional_information.'">'.$holu_additional_information.'</option>';

			}
		}else{
			foreach ($holu_additional_informations as $holu_additional_information) {

				$additional_information_options .= '<option '.(($holu_additional_information==$additional_information)?'selected':'').' value="'.$holu_additional_information.'">'.$holu_additional_information.'</option>';
				
			}
		}
		return $additional_information_options;
	}

	function get_sub_category_option($sub_categories_id, $categories_id){
		global $db;
		$sub_category_options = "";
		$query_portion = "";

		if($categories_id!="0"){
			$query_portion = " AND categories_id='$categories_id' ";
		}
		
		//reference type accessibility portion

		if($sub_categories_id=="0"){

			$sub_category_sq = $db->query("SELECT * FROM `sub_categories` WHERE deleted='0' $query_portion");
			if($sub_category_sq->rowCount()>0){
				$sub_category_options .= '<option selected value="">Select an option</option>';
				while($sub_category_row = $sub_category_sq->fetch()){
					
					if(check_access('sub_category_accessibility/'.get_col('categories', 'category_type', 'id', get_col('sub_categories', 'categories_id', 'id', $sub_category_row['id'])).'/'.get_col('sub_categories', 'categories_id', 'id', $sub_category_row['id']).'/'.$sub_category_row['id'].'/')==1){

						$sub_category_options .= '<option value="'.$sub_category_row['id'].'">'.$sub_category_row['sub_category_name'].'</option>';
					}
				}
			}
		}else{
			$sub_category_sq = $db->query("SELECT * FROM `sub_categories` WHERE deleted='0' $query_portion");
			if($sub_category_sq->rowCount()>0){
				$sub_category_options .= '<option selected value="">Select an option</option>';
				while($sub_category_row = $sub_category_sq->fetch()){
					if(check_access('sub_category_accessibility/'.get_col('categories', 'category_type', 'id', get_col('sub_categories', 'categories_id', 'id', $sub_category_row['id'])).'/'.get_col('sub_categories', 'categories_id', 'id', $sub_category_row['id']).'/'.$sub_category_row['id'].'/')==1){
						$sub_category_options .= '<option '.(($sub_category_row['id']==$sub_categories_id)?'selected':'').' value="'.$sub_category_row['id'].'">'.$sub_category_row['sub_category_name'].'</option>';
					}
					
				}
			}
		}
		return $sub_category_options;
	}

	function get_markup_option($path_specification, $markup){
		global $db;
		global $holu_users_id;
		$markup_options = "";
		
		if($markup==""){
			$accessibility_sq = $db->prepare(
			"SELECT *
			FROM `accessibilities`
			WHERE access_point LIKE :access_point
			AND is_accessed='1'
			AND deleted='0'
			AND system_users_id = :system_users_id"
			);

			$accessibility_sqx = $accessibility_sq->execute([
				'access_point'=>$path_specification.'%',
				'system_users_id'=>$holu_users_id
			]);

			if($accessibility_sq->rowCount()>0){
				$markup_options .= '<option value="">Select an option</option>';
				while ($accessibility_row = $accessibility_sq->fetch()){
					switch ($accessibility_row['access_point']) {
						case $path_specification.'tms_markup/':{
							$markup_options .= '<option value="TMS Markup">TMS Markup</option>';
						}break;

						case $path_specification.'qb_markup/':{
							$markup_options .= '<option value="QB Markup">QB Markup</option>';
							$markup_options .= '<option value="RQB Markup">RQB Markup</option>';
						}break;

						case $path_specification.'sib_markup/':{
							$markup_options .= '<option value="SIB Markup">SIB Markup</option>';
						}break;

						case $path_specification.'ad_markup/':{
							$markup_options .= '<option value="Ad Markup">Ad Markup</option>';
						}break;
						
						default:{
							$markup_options .= "";
						}break;
					}
				}
			}
		}else{
			$accessibility_sq = $db->prepare(
			"SELECT *
			FROM `accessibilities`
			WHERE access_point LIKE :access_point
			AND is_accessed='1'
			AND deleted='0'
			AND system_users_id = :system_users_id"
			);

			$accessibility_sqx = $accessibility_sq->execute([
				'access_point'=>$path_specification.'%',
				'system_users_id'=>$holu_users_id
			]);

			if($accessibility_sq->rowCount()>0){
				$markup_options .= '<option value="">Select an option</option>';
				while ($accessibility_row = $accessibility_sq->fetch()){
					switch ($accessibility_row['access_point']) {
						case $path_specification.'tms_markup/':{
							$markup_options .= '<option value="TMS Markup" '.(($markup=="TMS Markup")?'selected':'').' >TMS Markup</option>';
						}break;

						case $path_specification.'qb_markup/':{
							$markup_options .= '<option value="QB Markup" '.(($markup=="QB Markup")?'selected':'').' >QB Markup</option>';
							$markup_options .= '<option value="RQB Markup" '.(($markup=="RQB Markup")?'selected':'').' >RQB Markup</option>';
						}break;

						case $path_specification.'sib_markup/':{
							$markup_options .= '<option value="SIB Markup" '.(($markup=="SIB Markup")?'selected':'').' >SIB Markup</option>';
						}break;

						case $path_specification.'ad_markup/':{
							$markup_options .= '<option value="Ad Markup" '.(($markup=="Ad Markup")?'selected':'').' >Ad Markup</option>';
						}break;
						
						default:{
							$markup_options .= "";
						}break;
					}
				}
			}
		}

		
		return $markup_options;
	}

	function get_col($table, $column, $key, $value){
		global $db;
		$col = '';
		$table_sq = $db->query("SELECT $column FROM $table WHERE $key='$value' LIMIT 1");
		if($table_sq->rowCount()>0){
			$table_row = $table_sq->fetch();
			$col = $table_row[$column];
		}
		return $col;
	}

	function get_random_color() {
	  $part1 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	  $part2 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	  $part3 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
	    return '#'.$part1 . $part2 . $part3;
	}

	function holu_escape($input){
		return trim(htmlspecialchars((string)$input));
	}

	function check_access($access_path){
		
		$holu_accessibilities = strtolower($_SESSION['holu_accessibilities']);
		$access_path = strtolower($access_path);
		$result = "";
				
		if(strpos($holu_accessibilities, $access_path) !== false){
			$result = 1;
		}else{
			$result = 0;
		}
		return $result;
	}

	function print_sidebar($holu_portions){
		global $db;
		global $accessed_provinces;
		global $accessed_logistic_cashes;
		$sidebar = "";
		foreach ($holu_portions as $holu_portion) {
			if($holu_portion['type']=="sidebar"){
				if(check_access($holu_portion['path'])==1){

					$counter = '';
					switch ($holu_portion['path']) {
						case 'system_accessibility/request/report_cash_reservation/':{

							$cash_reservation_sq = $db->query("SELECT count(id) as num_pending FROM `cash_reservations` WHERE deleted='0' AND is_approved='0' AND logistic_cashes_id IN ($accessed_logistic_cashes)");
  						$cash_reservation_row = $cash_reservation_sq->fetch();

  						if($cash_reservation_row['num_pending']>0){
  							$counter = '
									<div class="badge badge-danger">
	            			'.$cash_reservation_row['num_pending'].'
	        				</div>
	        			';
  						}
							

						}break;

						case 'system_accessibility/request/report_purchase/':{

							$purchase_sq = $db->query("SELECT count(id) as num_pending FROM `purchases` WHERE deleted='0' AND is_approved='0' AND province IN ($accessed_provinces) AND logistic_cashes_id IN ($accessed_logistic_cashes)");
  							$purchase_row = $purchase_sq->fetch();

  						if($purchase_row['num_pending']>0){
  							$counter = '
									<div class="badge badge-danger">
	            			'.$purchase_row['num_pending'].'
	        				</div>
	        			';
  						}
							

						}break;

						case 'system_accessibility/request/report_purchase_include/':{

							$purchase_sq = $db->query("SELECT count(id) as num_pending FROM `purchases` WHERE deleted='0' AND is_approved='1' AND is_included='0' AND province IN ($accessed_provinces) AND logistic_cashes_id IN ($accessed_logistic_cashes)");
  						$purchase_row = $purchase_sq->fetch();

  						if($purchase_row['num_pending']>0){
  							$counter = '
									<div class="badge badge-danger">
	            			'.$purchase_row['num_pending'].'
	        				</div>
	        			';
  						}
							

						}break;

						case 'system_accessibility/request/report_transfer/':{

							$transfer_sq = $db->query("SELECT count(id) as num_pending FROM `transfers` WHERE deleted='0' AND is_approved='0' AND to_province IN ($accessed_provinces)");
  						$transfer_row = $transfer_sq->fetch();

  						if($transfer_row['num_pending']>0){
  							$counter = '
									<div class="badge badge-danger">
	            			'.$transfer_row['num_pending'].'
	        				</div>
	        			';
  						}
							

						}break;
						
						default:{

							$counter = '';

						}break;
					}

					if($holu_portion['url']!=""){
						if($holu_portion['icon']==""){
							$icon = '';
							$label = $holu_portion['label'];
						}else{
							$icon = '<i class="'.$holu_portion['icon'].'"></i>';
							$label = '<span> '.$holu_portion['label'].' </span>';
						}
						$sidebar .= '
						<li>
			        <a href="'.$holu_portion['url'].'" class="waves-effect">
			          '.$icon.'
			          '.$label.'
			          '.$counter.'
			        </a>
			      </li>
			      ';
					}else{
						if($holu_portion['icon']==""){
							$icon = '';
							$label = $holu_portion['label'];
							$arrow = '';
						}else{
							$icon = '<i class="'.$holu_portion['icon'].'"></i>';
							$label = '<span> '.$holu_portion['label'].' </span>';
							$arrow = '<span class="menu-arrow"></span>';
						}
						$sidebar .= '
						<li>
			        <a href="javascript: void(0);" class="waves-effect">
			          '.$icon.'
			          '.$label.'
			          '.$arrow.'
			        </a>
			        <ul class="nav-second-level" aria-expanded="false">
			        	'.print_sidebar($holu_portion['subs']).'
			        </ul>
			      </li>
			      ';
					}
				}
			}
			
		}
		return $sidebar;
	}


	function print_access_points($holu_portions){
		$access_points = "[";
		foreach ($holu_portions as $holu_portion) {
			if(sizeof($holu_portion['subs'])>0){
				$access_points .= '{ "id": "'.$holu_portion['path'].'", "text": "'.$holu_portion['label'].'", "children": '.print_access_points($holu_portion['subs']).' },';
			}else{
				$access_points .= '{ "id": "'.$holu_portion['path'].'", "text": "'.$holu_portion['label'].'"},';
			}
		}
		$access_points .= "]";
		return $access_points;
	}

	function print_access_provinces($holu_provinces){
		$access_points = "[";
		foreach ($holu_provinces as $holu_province) {
			$access_points .= '{ "id": "province_accessibility/'.$holu_province.'/", "text": "'.$holu_province.'" },';
		}
		$access_points .= "]";
		return $access_points;
	}

	function print_access_sub_categories(){
		global $db;
		$access_points = '[';

		$income_category_sq = $db->prepare("SELECT * FROM `categories`
			WHERE deleted='0'
			AND category_type='Income'
		");
		$income_category_sqx = $income_category_sq->execute([]);
		if($income_category_sq->rowCount()>0){
			$income_category_access_points = '[';
			while($income_category_row = $income_category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories`
					WHERE deleted='0'
					AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$income_category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					$sub_category_access_points = '[';
					while($sub_category_row = $sub_category_sq->fetch()){
						
						$sub_category_access_points .= '{ "id": "sub_category_accessibility/income/'.$income_category_row['id'].'/'.$sub_category_row['id'].'/", "text": "'.$sub_category_row['sub_category_name'].'" },';
					}
					$sub_category_access_points .= ']';
					$income_category_access_points .= '{ "id": "sub_category_accessibility/income/'.$income_category_row['id'].'", "text": "'.$income_category_row['category_name'].'", "children": '.$sub_category_access_points.' },';
				}else{
					$income_category_access_points .= '{ "id": "sub_category_accessibility/income/'.$income_category_row['id'].'/", "text": "'.$income_category_row['category_name'].'"},';
				}

			}
			$income_category_access_points .= ']';
			$access_points .= '{ "id": "sub_category_accessibility/income/", "text": "Income", "children": '.$income_category_access_points.' },';
		}else{
			$access_points .= '{ "id": "sub_category_accessibility/income/", "text": "Income"},';
		}

		$expense_category_sq = $db->prepare("SELECT * FROM `categories`
			WHERE deleted='0'
			AND category_type='Expense'
		");
		$expense_category_sqx = $expense_category_sq->execute([]);
		if($expense_category_sq->rowCount()>0){
			$expense_category_access_points = '[';
			while($expense_category_row = $expense_category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories`
					WHERE deleted='0'
					AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$expense_category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					$sub_category_access_points = '[';
					while($sub_category_row = $sub_category_sq->fetch()){
						
						$sub_category_access_points .= '{ "id": "sub_category_accessibility/expense/'.$expense_category_row['id'].'/'.$sub_category_row['id'].'/", "text": "'.$sub_category_row['sub_category_name'].'" },';
					}
					$sub_category_access_points .= ']';
					$expense_category_access_points .= '{ "id": "sub_category_accessibility/expense/'.$expense_category_row['id'].'", "text": "'.$expense_category_row['category_name'].'", "children": '.$sub_category_access_points.' },';
				}else{
					$expense_category_access_points .= '{ "id": "sub_category_accessibility/expense/'.$expense_category_row['id'].'/", "text": "'.$expense_category_row['category_name'].'"},';
				}
				
			}
			$expense_category_access_points .= ']';
			$access_points .= '{ "id": "sub_category_accessibility/expense/", "text": "Expense", "children": '.$expense_category_access_points.' },';
		}else{
			$access_points .= '{ "id": "sub_category_accessibility/expense/", "text": "Expense"},';
		}

		$exchange_category_sq = $db->prepare("SELECT * FROM `categories`
			WHERE deleted='0'
			AND category_type='Exchange'
		");
		$exchange_category_sqx = $exchange_category_sq->execute([]);
		if($exchange_category_sq->rowCount()>0){
			$exchange_category_access_points = '[';
			while($exchange_category_row = $exchange_category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories`
					WHERE deleted='0'
					AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$exchange_category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					$sub_category_access_points = '[';
					while($sub_category_row = $sub_category_sq->fetch()){
						
						$sub_category_access_points .= '{ "id": "sub_category_accessibility/exchange/'.$exchange_category_row['id'].'/'.$sub_category_row['id'].'/", "text": "'.$sub_category_row['sub_category_name'].'" },';
					}
					$sub_category_access_points .= ']';
					$exchange_category_access_points .= '{ "id": "sub_category_accessibility/exchange/'.$exchange_category_row['id'].'", "text": "'.$exchange_category_row['category_name'].'", "children": '.$sub_category_access_points.' },';
				}else{
					$exchange_category_access_points .= '{ "id": "sub_category_accessibility/exchange/'.$exchange_category_row['id'].'/", "text": "'.$exchange_category_row['category_name'].'"},';
				}
				
			}
			$exchange_category_access_points .= ']';
			$access_points .= '{ "id": "sub_category_accessibility/exchange/", "text": "Exchange", "children": '.$exchange_category_access_points.' },';
		}else{
			$access_points .= '{ "id": "sub_category_accessibility/exchange/", "text": "Exchange"},';
		}

		$purchase_category_sq = $db->prepare("SELECT * FROM `categories`
			WHERE deleted='0'
			AND category_type='Purchase'
		");
		$purchase_category_sqx = $purchase_category_sq->execute([]);
		if($purchase_category_sq->rowCount()>0){
			$purchase_category_access_points = '[';
			while($purchase_category_row = $purchase_category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories`
					WHERE deleted='0'
					AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$purchase_category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					$sub_category_access_points = '[';
					while($sub_category_row = $sub_category_sq->fetch()){
						
						$sub_category_access_points .= '{ "id": "sub_category_accessibility/purchase/'.$purchase_category_row['id'].'/'.$sub_category_row['id'].'/", "text": "'.$sub_category_row['sub_category_name'].'" },';
					}
					$sub_category_access_points .= ']';
					$purchase_category_access_points .= '{ "id": "sub_category_accessibility/purchase/'.$purchase_category_row['id'].'", "text": "'.$purchase_category_row['category_name'].'", "children": '.$sub_category_access_points.' },';
				}else{
					$purchase_category_access_points .= '{ "id": "sub_category_accessibility/purchase/'.$purchase_category_row['id'].'/", "text": "'.$purchase_category_row['category_name'].'"},';
				}
				
			}
			$purchase_category_access_points .= ']';
			$access_points .= '{ "id": "sub_category_accessibility/purchase/", "text": "Purchase", "children": '.$purchase_category_access_points.' },';
		}else{
			$access_points .= '{ "id": "sub_category_accessibility/purchase/", "text": "Purchase"},';
		}

		$transfer_category_sq = $db->prepare("SELECT * FROM `categories`
			WHERE deleted='0'
			AND category_type='Transfer'
		");
		$transfer_category_sqx = $transfer_category_sq->execute([]);
		if($transfer_category_sq->rowCount()>0){
			$transfer_category_access_points = '[';
			while($transfer_category_row = $transfer_category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories`
					WHERE deleted='0'
					AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$transfer_category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					$sub_category_access_points = '[';
					while($sub_category_row = $sub_category_sq->fetch()){
						
						$sub_category_access_points .= '{ "id": "sub_category_accessibility/transfer/'.$transfer_category_row['id'].'/'.$sub_category_row['id'].'/", "text": "'.$sub_category_row['sub_category_name'].'" },';
					}
					$sub_category_access_points .= ']';
					$transfer_category_access_points .= '{ "id": "sub_category_accessibility/transfer/'.$transfer_category_row['id'].'", "text": "'.$transfer_category_row['category_name'].'", "children": '.$sub_category_access_points.' },';
				}else{
					$transfer_category_access_points .= '{ "id": "sub_category_accessibility/transfer/'.$transfer_category_row['id'].'/", "text": "'.$transfer_category_row['category_name'].'"},';
				}
				
			}
			$transfer_category_access_points .= ']';
			$access_points .= '{ "id": "sub_category_accessibility/transfer/", "text": "Transfer", "children": '.$transfer_category_access_points.' },';
		}else{
			$access_points .= '{ "id": "sub_category_accessibility/transfer/", "text": "Transfer"},';
		}

		$access_points .= ']';
		return $access_points;
	}

	function print_access_logistic_cashes(){
		global $db;
		$access_points = '[';

		$logistic_cash_sq = $db->prepare("SELECT * FROM `logistic_cashes`
			WHERE deleted='0'
		");
		$logistic_cash_sqx = $logistic_cash_sq->execute([]);
		if($logistic_cash_sq->rowCount()>0){
			while($logistic_cash_row = $logistic_cash_sq->fetch()){

				$access_points .= '{ "id": "logistic_cash_accessibility/'.$logistic_cash_row['id'].'/", "text": "'.$logistic_cash_row['name'].'" },';

			}
			
		}

		$access_points .= ']';
		return $access_points;
	}

	// echo print_access_sub_categories();
	// exit();

	function escape_url_injection($holu_portions){
		foreach ($holu_portions as $holu_portion) {
			if(sizeof($holu_portion['subs'])>0){
				escape_url_injection($holu_portion['subs']);
			}else{
				if((basename($_SERVER['PHP_SELF'])==$holu_portion['url']) AND check_access($holu_portion['path'])==0){
					exit();
				}
			}
		}
	}

	function set_filtering_data($filtering_type){

		global $holu_filtering_data;
		global $holu_filtering_array;

		switch($filtering_type){
			case "income_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND incomes.province='".$province."' ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "income_date":
				if(isset($_GET['from_income_date']) AND !empty($_GET['from_income_date'])){
					$from_income_date = $_GET['from_income_date'];
					$holu_filtering_data .= " AND incomes.income_date>='".$from_income_date."' ";
					array_push($holu_filtering_array, "From: $from_income_date");
				}
				if(isset($_GET['to_income_date']) AND !empty($_GET['to_income_date'])){
					$to_income_date = $_GET['to_income_date'];
					$holu_filtering_data .= " AND incomes.income_date<='".$to_income_date."' ";
					array_push($holu_filtering_array, "To: $to_income_date");
				}
			break;

			case "income_categories_id":
				if(isset($_GET['categories_id']) AND !empty($_GET['categories_id'])){
					global $db;
					$categories_id = $_GET['categories_id'];
					$sub_categories = '';
					$sub_category_sq = $db->prepare(
						"SELECT id 
						FROM `sub_categories` 
						WHERE categories_id=:categories_id 
						AND deleted='0'"
					);
					$sub_category_sqx = $sub_category_sq->execute([
						'categories_id'=>$categories_id
					]);
					if($sub_category_sq->rowCount()>0){
						while($sub_category_row = $sub_category_sq->fetch()){
							$sub_categories .= '\''.$sub_category_row['id'].'\',';
						}
						$sub_categories = rtrim($sub_categories, ',');
					}
					
					
					$holu_filtering_data .= " AND incomes.sub_categories_id IN (".$sub_categories.") ";
					array_push($holu_filtering_array, "Category: ".get_col('categories', 'category_name', 'id', $categories_id));
				}
			break;

			case "income_sub_categories_id":
				if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
					$sub_categories_id = $_GET['sub_categories_id'];
					$holu_filtering_data .= " AND incomes.sub_categories_id='".$sub_categories_id."' ";
					array_push($holu_filtering_array, "Sub Category: $sub_categories_id");
				}
			break;

			case "expense_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND expenses.province='".$province."' ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "expense_date":
				if(isset($_GET['from_expense_date']) AND !empty($_GET['from_expense_date'])){
					$from_expense_date = $_GET['from_expense_date'];
					$holu_filtering_data .= " AND expenses.expense_date>='".$from_expense_date."' ";
					array_push($holu_filtering_array, "From: $from_expense_date");
				}
				if(isset($_GET['to_expense_date']) AND !empty($_GET['to_expense_date'])){
					$to_expense_date = $_GET['to_expense_date'];
					$holu_filtering_data .= " AND expenses.expense_date<='".$to_expense_date."' ";
					array_push($holu_filtering_array, "To: $to_expense_date");
				}
			break;

			case "expense_categories_id":
				if(isset($_GET['categories_id']) AND !empty($_GET['categories_id'])){
					global $db;
					$categories_id = $_GET['categories_id'];
					$sub_categories = '';
					$sub_category_sq = $db->prepare(
						"SELECT id 
						FROM `sub_categories` 
						WHERE categories_id=:categories_id 
						AND deleted='0'"
					);
					$sub_category_sqx = $sub_category_sq->execute([
						'categories_id'=>$categories_id
					]);
					if($sub_category_sq->rowCount()>0){
						while($sub_category_row = $sub_category_sq->fetch()){
							$sub_categories .= '\''.$sub_category_row['id'].'\',';
						}
						$sub_categories = rtrim($sub_categories, ',');
					}
					
					
					$holu_filtering_data .= " AND expenses.sub_categories_id IN (".$sub_categories.") ";
					array_push($holu_filtering_array, "Category: ".get_col('categories', 'category_name', 'id', $categories_id));
				}
			break;

			case "expense_sub_categories_id":
				if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
					$sub_categories_id = $_GET['sub_categories_id'];
					$holu_filtering_data .= " AND expenses.sub_categories_id='".$sub_categories_id."' ";
					array_push($holu_filtering_array, "Sub Category: $sub_categories_id");
				}
			break;

			case "purchase_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND purchases.province='".$province."' ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "purchase_date":
				if(isset($_GET['from_purchase_date']) AND !empty($_GET['from_purchase_date'])){
					$from_purchase_date = $_GET['from_purchase_date'];
					$holu_filtering_data .= " AND purchases.purchase_date>='".$from_purchase_date."' ";
					array_push($holu_filtering_array, "From: $from_purchase_date");
				}
				if(isset($_GET['to_purchase_date']) AND !empty($_GET['to_purchase_date'])){
					$to_purchase_date = $_GET['to_purchase_date'];
					$holu_filtering_data .= " AND purchases.purchase_date<='".$to_purchase_date."' ";
					array_push($holu_filtering_array, "To: $to_purchase_date");
				}
			break;

			case "purchase_categories_id":
				if(isset($_GET['categories_id']) AND !empty($_GET['categories_id'])){
					global $db;
					$categories_id = $_GET['categories_id'];
					$sub_categories = '';
					$sub_category_sq = $db->prepare(
						"SELECT id 
						FROM `sub_categories` 
						WHERE categories_id=:categories_id 
						AND deleted='0'"
					);
					$sub_category_sqx = $sub_category_sq->execute([
						'categories_id'=>$categories_id
					]);
					if($sub_category_sq->rowCount()>0){
						while($sub_category_row = $sub_category_sq->fetch()){
							$sub_categories .= '\''.$sub_category_row['id'].'\',';
						}
						$sub_categories = rtrim($sub_categories, ',');
					}
					
					
					$holu_filtering_data .= " AND purchases.sub_categories_id IN (".$sub_categories.") ";
					array_push($holu_filtering_array, "Category: ".get_col('categories', 'category_name', 'id', $categories_id));
				}
			break;

			case "purchase_sub_categories_id":
				if(isset($_GET['sub_categories_id']) AND !empty($_GET['sub_categories_id'])){
					$sub_categories_id = $_GET['sub_categories_id'];
					$holu_filtering_data .= " AND purchases.sub_categories_id='".$sub_categories_id."' ";
					array_push($holu_filtering_array, "Sub Category: $sub_categories_id");
				}
			break;

			case "exchange_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND exchanges.province='".$province."' ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "exchange_date":
				if(isset($_GET['from_exchange_date']) AND !empty($_GET['from_exchange_date'])){
					$from_exchange_date = $_GET['from_exchange_date'];
					$holu_filtering_data .= " AND exchanges.exchange_date>='".$from_exchange_date."' ";
					array_push($holu_filtering_array, "From: $from_exchange_date");
				}
				if(isset($_GET['to_exchange_date']) AND !empty($_GET['to_exchange_date'])){
					$to_exchange_date = $_GET['to_exchange_date'];
					$holu_filtering_data .= " AND exchanges.exchange_date<='".$to_exchange_date."' ";
					array_push($holu_filtering_array, "To: $to_exchange_date");
				}
			break;

			case "income_customer_name":
				if(isset($_GET['income_customer_name']) AND !empty($_GET['income_customer_name'])){
					$income_customer_name = $_GET['income_customer_name'];
					$holu_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE reference_type='Income' AND key_info='Customer Name' AND value_info='$income_customer_name' )  ";
					array_push($holu_filtering_array, "Customer Name: $income_customer_name");
				}
			break;

			case "income_customer_id":
				if(isset($_GET['income_customer_id']) AND !empty($_GET['income_customer_id'])){
					$income_customer_id = $_GET['income_customer_id'];
					$holu_filtering_data .= " AND incomes.id IN (SELECT reference_id FROM `additional_informations` WHERE deleted='0' AND reference_type='Income' AND key_info='Customer ID' AND value_info LIKE '%$income_customer_id' )  ";
					array_push($holu_filtering_array, "Customer ID: $income_customer_id");
				}
			break;

			case "income_currency":
				if(isset($_GET['income_currency']) AND !empty($_GET['income_currency'])){
					$income_currency = $_GET['income_currency'];
					$holu_filtering_data .= " AND incomes.currency='".$income_currency."' ";
					array_push($holu_filtering_array, "Currency: $income_currency");
				}
			break;

			case "income_amount":
				if(isset($_GET['income_amount']) AND !empty($_GET['income_amount'])){
					$income_amount = $_GET['income_amount'];
					$holu_filtering_data .= " AND incomes.income_amount='".$income_amount."' ";
					array_push($holu_filtering_array, "Amount: $income_amount");
				}
			break;

			case "expense_currency":
				if(isset($_GET['expense_currency']) AND !empty($_GET['expense_currency'])){
					$expense_currency = $_GET['expense_currency'];
					$holu_filtering_data .= " AND expenses.currency='".$expense_currency."' ";
					array_push($holu_filtering_array, "Currency: $expense_currency");
				}
			break;

			case "expense_amount":
				if(isset($_GET['expense_amount']) AND !empty($_GET['expense_amount'])){
					$expense_amount = $_GET['expense_amount'];
					$holu_filtering_data .= " AND expenses.expense_amount='".$expense_amount."' ";
					array_push($holu_filtering_array, "Amount: $expense_amount");
				}
			break;

			case "expense_is_printed":
				if(isset($_GET['is_printed']) AND !empty($_GET['is_printed'])){
					$is_printed = $_GET['is_printed'];
					if($is_printed=="Yes"){
						$holu_filtering_data .= " AND expenses.id IN (SELECT reference_id FROM `invoices` WHERE deleted='0' AND reference_type='Expense' )  ";
					}else if($is_printed=="No"){
						$holu_filtering_data .= " AND expenses.id NOT IN (SELECT reference_id FROM `invoices` WHERE deleted='0' AND reference_type='Expense' )  ";
					}
					array_push($holu_filtering_array, "Is Printed: $is_printed");
				}
			break;

			case "purchase_currency":
				if(isset($_GET['purchase_currency']) AND !empty($_GET['purchase_currency'])){
					$purchase_currency = $_GET['purchase_currency'];
					$holu_filtering_data .= " AND purchases.currency='".$purchase_currency."' ";
					array_push($holu_filtering_array, "Currency: $purchase_currency");
				}
			break;

			case "purchase_amount":
				if(isset($_GET['purchase_amount']) AND !empty($_GET['purchase_amount'])){
					$purchase_amount = $_GET['purchase_amount'];
					$holu_filtering_data .= " AND purchases.purchase_amount='".$purchase_amount."' ";
					array_push($holu_filtering_array, "Amount: $purchase_amount");
				}
			break;

			case "purchase_is_printed":
				if(isset($_GET['is_printed']) AND !empty($_GET['is_printed'])){
					$is_printed = $_GET['is_printed'];
					if($is_printed=="Yes"){
						$holu_filtering_data .= " AND purchases.id IN (SELECT reference_id FROM `invoices` WHERE deleted='0' AND reference_type='Purchase' )  ";
					}else if($is_printed=="No"){
						$holu_filtering_data .= " AND purchases.id NOT IN (SELECT reference_id FROM `invoices` WHERE deleted='0' AND reference_type='Purchase' )  ";
					}
					array_push($holu_filtering_array, "Is Printed: $is_printed");
				}
			break;

			case "purchase_users_id":
				if(isset($_GET['users_id']) AND !empty($_GET['users_id'])){
					$users_id = $_GET['users_id'];
					$holu_filtering_data .= " AND purchases.users_id='".$users_id."' ";
					array_push($holu_filtering_array, "Added By: ".get_col('users', 'username', 'id', $users_id));
				}
			break;

			case "reservation_logistic_cashes_id":
				if(isset($_GET['logistic_cashes_id']) AND !empty($_GET['logistic_cashes_id'])){
					$logistic_cashes_id = $_GET['logistic_cashes_id'];
					$holu_filtering_data .= " AND cash_reservations.logistic_cashes_id='".$logistic_cashes_id."' ";
					array_push($holu_filtering_array, "Logistic Cash: $logistic_cashes_id");
				}
			break;

			case "reservation_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND cash_reservations.province='".$province."' ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "reservation_date":
				if(isset($_GET['from_reservation_date']) AND !empty($_GET['from_reservation_date'])){
					$from_reservation_date = $_GET['from_reservation_date'];
					$holu_filtering_data .= " AND cash_reservations.reservation_date>='".$from_reservation_date."' ";
					array_push($holu_filtering_array, "From: $from_reservation_date");
				}
				if(isset($_GET['to_reservation_date']) AND !empty($_GET['to_reservation_date'])){
					$to_reservation_date = $_GET['to_reservation_date'];
					$holu_filtering_data .= " AND cash_reservations.reservation_date<='".$to_reservation_date."' ";
					array_push($holu_filtering_array, "To: $to_reservation_date");
				}
			break;

			case "reservation_currency":
				if(isset($_GET['currency']) AND !empty($_GET['currency'])){
					$currency = $_GET['currency'];
					$holu_filtering_data .= " AND cash_reservations.currency='".$currency."' ";
					array_push($holu_filtering_array, "Currency: $currency");
				}
			break;

			case "reservation_amount":
				if(isset($_GET['reservation_amount']) AND !empty($_GET['reservation_amount'])){
					$reservation_amount = $_GET['reservation_amount'];
					$holu_filtering_data .= " AND cash_reservations.reservation_amount='".$reservation_amount."' ";
					array_push($holu_filtering_array, "Amount: $reservation_amount");
				}
			break;

			case "transfer_province":
				if(isset($_GET['province']) AND !empty($_GET['province'])){
					$province = $_GET['province'];
					$holu_filtering_data .= " AND (transfers.from_province='".$province."' OR transfers.to_province='".$province."') ";
					array_push($holu_filtering_array, "Province: $province");
				}
			break;

			case "transfer_date":
				if(isset($_GET['from_transfer_date']) AND !empty($_GET['from_transfer_date'])){
					$from_transfer_date = $_GET['from_transfer_date'];
					$holu_filtering_data .= " AND transfers.transfer_date>='".$from_transfer_date."' ";
					array_push($holu_filtering_array, "From: $from_transfer_date");
				}
				if(isset($_GET['to_transfer_date']) AND !empty($_GET['to_transfer_date'])){
					$to_transfer_date = $_GET['to_transfer_date'];
					$holu_filtering_data .= " AND transfers.transfer_date<='".$to_transfer_date."' ";
					array_push($holu_filtering_array, "To: $to_transfer_date");
				}
			break;

			default:{

			}break;
		}

	}

	function get_table_header($icon, $label, $meta, $meta2, array $filter_items){
		$header = '';

		$header .= '<i class="'.$icon.'"></i> ';
		$header .= $label;
		$header .= '
		<a class="append text-info tip" data-tip="filter_info" data-placement="right">
	    <span class="badge badge-dark">
	      '.$meta.' of '.$meta2.'
	    </span>
	  </a>
	  ';
	  if(sizeof($filter_items)>0){
	  	$header .= '
		  <sapn id="filter_info" class="tip-content hidden">
	    ';
	  	foreach ($filter_items as $filter_item) {
	  		$header .= '<p style="white-space: nowrap;">'.$filter_item.'</p>';
	  	}
	  	$header .= '
	    </sapn>
	    ';
		  }
    return $header;
	}

	function set_pagination(){
		global $holu_num_record_per_page;
		global $holu_from;
		global $holu_to;
		global $holu_count;
		if(isset($_GET['page'])){
	    $holu_page = $_GET['page'];
	    $holu_from = $holu_page * $holu_num_record_per_page - $holu_num_record_per_page;
	    $holu_to   = $holu_num_record_per_page;
	    $holu_count=$holu_page * $holu_num_record_per_page - $holu_num_record_per_page + 1;
	  }else{
	    $holu_from = 0;
	    $holu_to   = $holu_num_record_per_page;
	    $holu_count=1;
	  }
	}

	function set_page_numbers(){
		global $pagination;
		global $holu_num_record_per_page;
		global $record;
		$pagination->records($record);
    $pagination->records_per_page($holu_num_record_per_page);
    // render the pagination links
    if($record>$holu_num_record_per_page){
      $pagination->render();
    }
	}

	function set_referer($referer){
		$result = "";
		if(strpos($referer, "?")!==false){
			$url_portions = explode('?', $referer);
			$datas = explode('&', $url_portions[1]);
			for($i=0; $i<sizeof($datas); $i++){
				if($datas[$i]=="success" OR $datas[$i]=="error" OR $datas[$i]=="duplicated"){
					unset($datas[$i]);
				}
			}
			if(sizeof($datas)>0){
				$datas = implode('&', $datas);
				$result = $url_portions[0]."?".$datas."&";
			}else{
				$result = $url_portions[0]."?";
			}
		}else{
			$result = $referer."?";
		}
		return $result;
	}

	function set_province_portion(){
		global $holu_provinces;
		$result = "";
		foreach($holu_provinces as $holu_province){
			$access_point = 'province_accessibility/'.$holu_province.'/';
			if(check_access($access_point)==1){
				$result .= '\''.$holu_province.'\',';
			}
		}
		$result = rtrim($result, ",");
		return $result;
	}

	function set_sub_category_portion($category_type){
		global $db;
		$result = "";

		//reference type accessibility portion
		$rtap = '';

		switch($category_type){
			case "Income":{
				$rtap = 'income';
			}break;
			case "Expense":{
				$rtap = 'expense';
			}break;
			case "Exchange":{
				$rtap = 'exchange';
			}break;
			case "Purchase":{
				$rtap = 'purchase';
			}break;
		}

		$category_sq = $db->prepare("SELECT * FROM `categories` 
		WHERE deleted='0'
		AND category_type=:category_type
		");
		$category_sqx = $category_sq->execute([
			'category_type'=>$category_type
		]);
		if($category_sq->rowCount()>0){
			while($category_row = $category_sq->fetch()){

				$sub_category_sq = $db->prepare("SELECT * FROM `sub_categories` 
				WHERE deleted='0'
				AND categories_id=:categories_id
				");
				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$category_row['id']
				]);
				if($sub_category_sq->rowCount()>0){
					while($sub_category_row = $sub_category_sq->fetch()){
						$access_point = 'sub_category_accessibility/'.$rtap.'/'.$category_row['id'].'/'.$sub_category_row['id'].'/';
						if(check_access($access_point)==1){
							$result .= '\''.$sub_category_row['id'].'\',';
						}
					}
				}
			}
		}
		if($result!=""){
			$result = rtrim($result, ",");
		}else{
			$result = '\'\'';
		}
		$result = rtrim($result, ",");
		return $result;
	}

	function set_logistic_cash_portion(){
		global $db;
		$result = "";

		$logistic_cash_sq = $db->prepare("SELECT * FROM `logistic_cashes` 
		WHERE deleted='0'
		");

		$logistic_cash_sqx = $logistic_cash_sq->execute([]);
		if($logistic_cash_sq->rowCount()>0){
			while($logistic_cash_row = $logistic_cash_sq->fetch()){

				
				$access_point = 'logistic_cash_accessibility/'.$logistic_cash_row['id'].'/';
				if(check_access($access_point)==1){
					$result .= '\''.$logistic_cash_row['id'].'\',';
				}

			}
		}

		if($result!=""){
			$result = rtrim($result, ",");
		}else{
			$result = '\'\'';
		}

		$result = rtrim($result, ",");
		return $result;

	}



	function set_markups($rtap, $reference_type, $reference_id){
		global $db;
		global $holu_users_id;
		$result = '';

		if(check_access($rtap.'tms_markup')==1){
			$markup_sq1 = $db->prepare("SELECT deleted FROM `markups` 
				WHERE reference_type=:reference_type
				AND reference_id=:reference_id
				AND markup_type=:markup_type
			");

			$markup_sqx1 = $markup_sq1->execute([
				'reference_type'=>$reference_type,
				'reference_id'=>$reference_id,
				'markup_type'=>'TMS Markup'
			]);

			if($markup_sq1->rowCount()>0){
				$markup_row1 = $markup_sq1->fetch();
				if($markup_row1['deleted']=='0'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'TMS Markup\');">TMS Markup</span>
					';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'TMS Markup\');">TMS Markup</span>
					';
				}
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'TMS Markup\');">TMS Markup</span>
				';
			}
		}

		if(check_access($rtap.'qb_markup')==1){

			if($reference_type=="Transfer"){

				$markup_sq2 = $db->prepare("SELECT deleted FROM `markups` 
					WHERE reference_type=:reference_type
					AND reference_id=:reference_id
					AND markup_type=:markup_type
				");

				$markup_sqx2 = $markup_sq2->execute([
					'reference_type'=>$reference_type,
					'reference_id'=>$reference_id,
					'markup_type'=>'QB Markup'
				]);

				if($markup_sq2->rowCount()>0){
					$markup_row2 = $markup_sq2->fetch();
					if($markup_row2['deleted']=='0'){
						$result .= '
							<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
						';
					}else{
						$result .= '
							<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
						';
					}
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
					';
				}

				$r_markup_sq2 = $db->prepare("SELECT deleted FROM `markups` 
					WHERE reference_type=:reference_type
					AND reference_id=:reference_id
					AND markup_type=:markup_type
				");

				$r_markup_sqx2 = $r_markup_sq2->execute([
					'reference_type'=>$reference_type,
					'reference_id'=>$reference_id,
					'markup_type'=>'RQB Markup'
				]);

				if($r_markup_sq2->rowCount()>0){
					$r_markup_row2 = $r_markup_sq2->fetch();
					if($r_markup_row2['deleted']=='0'){
						$result .= '
							<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'RQB Markup\');">RQB Markup</span>
						';
					}else{
						$result .= '
							<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'RQB Markup\');">RQB Markup</span>
						';
					}
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'RQB Markup\');">RQB Markup</span>
					';
				}
			}else{
				$markup_sq2 = $db->prepare("SELECT deleted FROM `markups` 
					WHERE reference_type=:reference_type
					AND reference_id=:reference_id
					AND markup_type=:markup_type
				");

				$markup_sqx2 = $markup_sq2->execute([
					'reference_type'=>$reference_type,
					'reference_id'=>$reference_id,
					'markup_type'=>'QB Markup'
				]);

				if($markup_sq2->rowCount()>0){
					$markup_row2 = $markup_sq2->fetch();
					if($markup_row2['deleted']=='0'){
						$result .= '
							<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
						';
					}else{
						$result .= '
							<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
						';
					}
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>
					';
				}
			}

			

		}

		if(check_access($rtap.'sib_markup')==1){

			$markup_sq3 = $db->prepare("SELECT deleted FROM `markups` 
				WHERE reference_type=:reference_type
				AND reference_id=:reference_id
				AND markup_type=:markup_type
			");

			$markup_sqx3 = $markup_sq3->execute([
				'reference_type'=>$reference_type,
				'reference_id'=>$reference_id,
				'markup_type'=>'SIB Markup'
			]);

			if($markup_sq3->rowCount()>0){
				$markup_row3 = $markup_sq3->fetch();
				if($markup_row3['deleted']=='0'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'SIB Markup\');">SIB Markup</span>
					';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'SIB Markup\');">SIB Markup</span>
					';
				}
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'SIB Markup\');">SIB Markup</span>
				';
			}
		}

		if(check_access($rtap.'ad_markup')==1){

			$markup_sq4 = $db->prepare("SELECT deleted FROM `markups` 
				WHERE reference_type=:reference_type
				AND reference_id=:reference_id
				AND markup_type=:markup_type
			");

			$markup_sqx4 = $markup_sq4->execute([
				'reference_type'=>$reference_type,
				'reference_id'=>$reference_id,
				'markup_type'=>'Ad Markup'
			]);

			if($markup_sq4->rowCount()>0){
				$markup_row4 = $markup_sq4->fetch();
				if($markup_row4['deleted']=='0'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'Ad Markup\');">Ad Markup</span>
					';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'Ad Markup\');">Ad Markup</span>
					';
				}
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'Ad Markup\');">Ad Markup</span>
				';
			}

		}

		return $result;
	}

	function get_markups($rtap, $reference_type, $reference_id,
						 $tms_markup, $qb_markup, $sib_markup, $ad_markup){
		$result = '';

		if(check_access($rtap.'tms_markup')==1){
						
			if($tms_markup=='1'){
				$result .= '
					<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'TMS Markup\');">TMS Markup</span>';
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'TMS Markup\');">TMS Markup</span>';
			}
			
		}

		if(check_access($rtap.'qb_markup')==1){

			if($reference_type=="Transfer"){

				$qb_markups = explode(',', $qb_markup);

				$sqb_markup = $qb_markups[0];
				$rqb_markup = $qb_markups[1];

				if($sqb_markup=='1'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>';
				}

				if($rqb_markup=='1'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'RQB Markup\');">RQB Markup</span>';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'RQB Markup\');">RQB Markup</span>';
				}

			}else{

				if($qb_markup=='1'){
					$result .= '
						<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>';
				}else{
					$result .= '
						<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'QB Markup\');">QB Markup</span>';
				}

			}
			
		}

		if(check_access($rtap.'sib_markup')==1){

			
			if($sib_markup=='1'){
				$result .= '
					<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'SIB Markup\');">SIB Markup</span>';
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'SIB Markup\');">SIB Markup</span>';
			}
		}

		if(check_access($rtap.'ad_markup')==1){

			
			if($ad_markup=='1'){
				$result .= '
					<span class="badge badge-success holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'Ad Markup\');">Ad Markup</span>';
			}else{
				$result .= '
					<span class="badge badge-secondary holu_markup_items" onclick="markup_item(\''.$rtap.'\', \''.$reference_type.'\', \''.$reference_id.'\', \'Ad Markup\');">Ad Markup</span>';
			}
			

		}

		return $result;
	}

	function add_row($selector, $counter, $value){
		$result = '';
		$label = '';
		$button = '';
		switch ($selector) {
			case 'additional_information':{
				$label = 'Additional Information';
				if($counter==1){
					$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($counter+1).', \''.$value.'\');"><i class="fa fa-plus"></i></button>';
				}else{
					$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
				}
				
				$result .= '
				<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
					<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
					<div class="col-sm-3">
						<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
							'.get_additional_information_option("0").'
						</select>
					</div>
					<div class="col-sm-3">
						<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="'.$value.'">
					</div>
					'.$button.'
				</div>
				';
			}break;

			case 'edit_additional_information':{
				global $db;
				

				if($value!=""){
					$label = 'Additional Information';
					$additional_information_sq = $db->prepare(
          	"SELECT *
          	FROM `additional_informations`
          	WHERE reference_type='Income'
          	AND reference_id=:reference_id
          	AND deleted='0'"
          );

          $additional_information_sqx = $additional_information_sq->execute([
          	'reference_id'=>$value
          ]);

          if($additional_information_sq->rowCount()>0){
          	while ($additional_information_row = $additional_information_sq->fetch()) {
          		if($counter==1){
								$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($additional_information_sq->rowCount()+1).', \'\');"><i class="fa fa-plus"></i></button>';
							}else if($counter==$additional_information_sq->rowCount()){
								$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
							}else{
								$button = '<button class="holu_remover_btn hidden" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
							}
          		$result .= '
							<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
								<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
								<div class="col-sm-3">
									<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
										'.get_additional_information_option($additional_information_row['key_info']).'
									</select>
								</div>
								<div class="col-sm-3">
									<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="'.$additional_information_row['value_info'].'">
								</div>
								'.$button.'
							</div>
							';
							$counter++;
          	}
          }else{
          	$label = 'Additional Information';
						if($counter==1){
							$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($counter+1).', \''.$value.'\');"><i class="fa fa-plus"></i></button>';
						}else{
							$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
						}
						
						$result .= '
						<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
							<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
							<div class="col-sm-3">
								<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
									'.get_additional_information_option("0").'
								</select>
							</div>
							<div class="col-sm-3">
								<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="">
							</div>
							'.$button.'
						</div>
						';
          }
				}else{
					$label = 'Additional Information';
					if($counter==1){
						$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($counter+1).', \''.$value.'\');"><i class="fa fa-plus"></i></button>';
					}else{
						$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
					}
					
					$result .= '
					<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
						<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
						<div class="col-sm-3">
							<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
								'.get_additional_information_option("0").'
							</select>
						</div>
						<div class="col-sm-3">
							<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="'.$value.'">
						</div>
						'.$button.'
					</div>
					';
				}
				
				
				
				
			}break;

			case 'edit_x_additional_information':{
				global $db;
				

				if($value!=""){
					$label = 'Additional Information';
					$additional_information_sq = $db->prepare(
          	"SELECT *
          	FROM `additional_informations`
          	WHERE reference_type='Expense'
          	AND reference_id=:reference_id
          	AND deleted='0'"
          );

          $additional_information_sqx = $additional_information_sq->execute([
          	'reference_id'=>$value
          ]);

          if($additional_information_sq->rowCount()>0){
          	while ($additional_information_row = $additional_information_sq->fetch()) {
          		if($counter==1){
								$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($additional_information_sq->rowCount()+1).', \'\');"><i class="fa fa-plus"></i></button>';
							}else if($counter==$additional_information_sq->rowCount()){
								$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
							}else{
								$button = '<button class="holu_remover_btn hidden" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
							}
          		$result .= '
							<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
								<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
								<div class="col-sm-3">
									<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
										'.get_additional_information_option($additional_information_row['key_info']).'
									</select>
								</div>
								<div class="col-sm-3">
									<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="'.$additional_information_row['value_info'].'">
								</div>
								'.$button.'
							</div>
							';
							$counter++;
          	}
          }else{
          	$label = 'Additional Information';
						if($counter==1){
							$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($counter+1).', \''.$value.'\');"><i class="fa fa-plus"></i></button>';
						}else{
							$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
						}
						
						$result .= '
						<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
							<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
							<div class="col-sm-3">
								<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
									'.get_additional_information_option("0").'
								</select>
							</div>
							<div class="col-sm-3">
								<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="">
							</div>
							'.$button.'
						</div>
						';
          }
				}else{
					$label = 'Additional Information';
					if($counter==1){
						$button = '<button class="holu_adder_btn" type="button" id="'.$selector.'_adder_button" onclick="add_row( \''.$selector.'\', '.($counter+1).', \''.$value.'\');"><i class="fa fa-plus"></i></button>';
					}else{
						$button = '<button class="holu_remover_btn" id="'.$selector.'_remover_button_'.$counter.'" type="button" onclick="remove_row( \''.$selector.'\', '.$counter.')"><i class="fa fa-minus"></i></button>';
					}
					
					$result .= '
					<div class="form-group row" id="'.$selector.'_container_'.$counter.'">
						<label class="col-sm-3 col-form-label">'.$label.' '.$counter.'</label>
						<div class="col-sm-3">
							<select id="key_'.$selector.'_'.$counter.'" name="key_infos[]" class="form-control" onchange="configure_input_field(\''.$selector.'\', \''.$counter.'\');">
								'.get_additional_information_option("0").'
							</select>
						</div>
						<div class="col-sm-3">
							<input type="text" id="value_'.$selector.'_'.$counter.'" name="value_infos[]" class="form-control" placeholder="Type here..." value="'.$value.'">
						</div>
						'.$button.'
					</div>
					';
				}
				
				
				
				
			}break;
			
			default:{

			}break;
		}
		return $result;
	}

	function check_duplicate_income($post){
		global $db;
		$flag = 0;
		$additional_informations_counter = 0;
		$variable_portion = "";
		$condition_portion = "";

		$province = holu_escape($post['province']);
    $sub_categories_id = holu_escape($post['sub_categories_id']);
    $income_date = holu_escape($post['income_date']);
		$income_amount = holu_escape($post['income_amount']);
    $currency = holu_escape($post['currency']);
    $description = holu_escape($post['description']);

    
    

    $key_infos = array();
    $value_infos = array();

    if(!empty($post['customer_name'])){
    	array_push($key_infos, 'Customer Name');
    	array_push($value_infos, holu_escape($post['customer_name']));
    }
    if(!empty($post['customer_id'])){
    	array_push($key_infos, 'Customer ID');
    	array_push($value_infos, holu_escape($post['customer_id']));
    }
    if(!empty($post['package'])){
    	array_push($key_infos, 'Package');
    	array_push($value_infos, holu_escape($post['package']));
    }
    if(!empty($post['start_date'])){
    	array_push($key_infos, 'Start Date');
    	array_push($value_infos, holu_escape($post['start_date']));
    }
    if(!empty($post['end_date'])){
    	array_push($key_infos, 'End Date');
    	array_push($value_infos, holu_escape($post['end_date']));
    }
    if(!empty($post['equipment'])){
    	array_push($key_infos, 'Equipment');
    	array_push($value_infos, holu_escape($post['equipment']));
    }
    if(!empty($post['other_services'])){
    	array_push($key_infos, 'Other Services');
    	array_push($value_infos, holu_escape($post['other_services']));
    }
    if(!empty($post['employee'])){
    	array_push($key_infos, 'Employee');
    	array_push($value_infos, holu_escape($post['employee']));
    }

   

    if(isset($post['data_id']) AND !empty($post['data_id'])){
    	$incomes_id = holu_escape(holu_decode($post['data_id']));

    	if(sizeof($key_infos)>0){
	    	for ($i=0; $i<sizeof($key_infos); $i++) {
		
		    	$key_info = holu_escape($key_infos[$i]);
		    	$value_info = holu_escape($value_infos[$i]);
		    	$additional_informations_counter = $additional_informations_counter+1;

		    	if($key_info!=""){
		    		switch ($key_info) {

			    		case 'Customer Name':{
					      $char = "customer_name";
					    }break;
					    case 'Customer ID':{
					      $char = "customer_id";
					    }break;
					    case 'Package':{
					      $char = "package";
					    }break;
					    case 'Start Date':{
					      $char = "start_date";
					    }break;
					    case 'End Date':{
					      $char = "end_date";
					    }break;
					    case 'Equipment':{
					    	$char = "equipment";
					    }break;
					    case 'Other Services':{
					    	$char = "other_services";
					    }break;
					    default:{
					    	$char = "other";
					    }break;
			    	}

				    $variable_portion .= " ,
					    (
				        SELECT
			            reference_id,
			            value_info AS ".$char."
				        FROM
			            additional_informations
				        WHERE
			            reference_type = 'Income' 
			            AND key_info = '".$key_info."'
			            AND value_info = '".$value_info."'
					    ) AS ".$char."_additional_informations".$additional_informations_counter." "; 

						$condition_portion .= " AND incomes.id = ".$char."_additional_informations".$additional_informations_counter.".reference_id ";
		    	}

		    	
		    	
		    }
	    }
    	
    	$income_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    (
		        SELECT
		        	id,
		        	deleted,
	            province,
	            sub_categories_id,
	            income_date,
	            income_amount,
	            currency,
	            description
		        FROM incomes
			    ) AS incomes
					$variable_portion
				WHERE
					incomes.deleted='0'
					AND incomes.province=:province
					AND incomes.sub_categories_id=:sub_categories_id
					AND incomes.income_date=:income_date
					AND incomes.income_amount=:income_amount
					AND incomes.currency=:currency
					AND incomes.description=:description
					AND incomes.id!=:incomes_id
					$condition_portion
				  "
			);

			$income_sqx = $income_sq->execute([
		  	'province'=>$province,
		  	'sub_categories_id'=>$sub_categories_id,
		  	'income_date'=>$income_date,
		  	'income_amount'=>$income_amount,
		  	'currency'=>$currency,
		  	'description'=>$description,
		  	'incomes_id'=>$incomes_id
		  ]);

    }else{

    	if(sizeof($key_infos)>0){
	    	for ($i=0; $i<sizeof($key_infos); $i++) {
		
		    	$key_info = holu_escape($key_infos[$i]);
		    	$value_info = holu_escape($value_infos[$i]);

		    	if($key_info!=""){
		    		switch ($key_info) {

			    		case 'Customer Name':{
					      $char = "customer_name";
					    }break;
					    case 'Customer ID':{
					      $char = "customer_id";
					    }break;
					    case 'Package':{
					      $char = "package";
					    }break;
					    case 'Start Date':{
					      $char = "start_date";
					    }break;
					    case 'End Date':{
					      $char = "end_date";
					    }break;
					    case 'Equipment':{
					    	$char = "equipment";
					    }break;
					    case 'Other Services':{
					    	$char = "other_services";
					    }break;
					    default:{
					    	$char = "default";
					    }break;
			    	}

				    $variable_portion .= " ,
					    (
				        SELECT
			            reference_id,
			            value_info AS ".$char."
				        FROM
			            additional_informations
				        WHERE
			            reference_type = 'Income' 
			            AND key_info = '".$key_info."'
			            AND value_info = '".$value_info."'
					    ) AS ".$char."_additional_informations"; 

						$condition_portion .= " AND incomes.id = ".$char."_additional_informations.reference_id ";
		    	}

		    	
		    	
		    }
	    }

    	$income_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    (
		        SELECT
		        	id,
		        	deleted,
	            province,
	            sub_categories_id,
	            income_date,
	            income_amount,
	            currency,
	            description
		        FROM incomes
			    ) AS incomes
					$variable_portion
				WHERE
					incomes.deleted='0'
					AND incomes.province=:province
					AND incomes.sub_categories_id=:sub_categories_id
					AND incomes.income_date=:income_date
					AND incomes.income_amount=:income_amount
					AND incomes.currency=:currency
					AND incomes.description=:description
					$condition_portion"
			);

			$income_sqx = $income_sq->execute([
		  	'province'=>$province,
		  	'sub_categories_id'=>$sub_categories_id,
		  	'income_date'=>$income_date,
		  	'income_amount'=>$income_amount,
		  	'currency'=>$currency,
		  	'description'=>$description
		  ]);

    }

		



	  if($income_sq->rowCount()>0){
	  	$flag = 1;
	  }

	  return $flag;
	}

	function check_duplicate_expense($post){
		global $db;
		$flag = 0;
		$additional_informations_counter = 0;
		$variable_portion = "";
		$condition_portion = "";

		$province = holu_escape($post['province']);
    $sub_categories_id = holu_escape($post['sub_categories_id']);
    $expense_date = holu_escape($post['expense_date']);
		$expense_amount = holu_escape($post['expense_amount']);
    $currency = holu_escape($post['currency']);
    $description = holu_escape($post['description']);

    $key_infos = array();
    $value_infos = array();

    if(!empty($post['customer_name'])){
    	array_push($key_infos, 'Customer Name');
    	array_push($value_infos, holu_escape($post['customer_name']));
    }
    if(!empty($post['customer_id'])){
    	array_push($key_infos, 'Customer ID');
    	array_push($value_infos, holu_escape($post['customer_id']));
    }
    if(!empty($post['package'])){
    	array_push($key_infos, 'Package');
    	array_push($value_infos, holu_escape($post['package']));
    }
    if(!empty($post['start_date'])){
    	array_push($key_infos, 'Start Date');
    	array_push($value_infos, holu_escape($post['start_date']));
    }
    if(!empty($post['end_date'])){
    	array_push($key_infos, 'End Date');
    	array_push($value_infos, holu_escape($post['end_date']));
    }
    if(!empty($post['equipment'])){
    	array_push($key_infos, 'Equipment');
    	array_push($value_infos, holu_escape($post['equipment']));
    }
    if(!empty($post['other_services'])){
    	array_push($key_infos, 'Other Services');
    	array_push($value_infos, holu_escape($post['other_services']));
    }
    if(!empty($post['employee'])){
    	array_push($key_infos, 'Employee');
    	array_push($value_infos, holu_escape($post['employee']));
    }
    

    

   

    if(isset($post['data_id']) AND !empty($post['data_id'])){
    	$expenses_id = holu_escape(holu_decode($post['data_id']));

    	if(sizeof($key_infos)>0){
	    	for ($i=0; $i<sizeof($key_infos); $i++) {
		
		    	$key_info = holu_escape($key_infos[$i]);
		    	$value_info = holu_escape($value_infos[$i]);
		    	$additional_informations_counter = $additional_informations_counter+1;

		    	if($key_info!=""){
		    		switch ($key_info) {

			    		case 'Customer Name':{
					      $char = "customer_name";
					    }break;
					    case 'Customer ID':{
					      $char = "customer_id";
					    }break;
					    case 'Package':{
					      $char = "package";
					    }break;
					    case 'Start Date':{
					      $char = "start_date";
					    }break;
					    case 'End Date':{
					      $char = "end_date";
					    }break;
					    case 'Equipment':{
					    	$char = "equipment";
					    }break;
					    case 'Other Services':{
					    	$char = "other_services";
					    }break;
					    default:{
					    	$char = "other";
					    }break;
			    	}

				    $variable_portion .= " ,
					    (
				        SELECT
			            reference_id,
			            value_info AS ".$char."
				        FROM
			            additional_informations
				        WHERE
			            reference_type = 'Expense' 
			            AND key_info = '".$key_info."'
			            AND value_info = '".$value_info."'
					    ) AS ".$char."_additional_informations".$additional_informations_counter." "; 

						$condition_portion .= " AND expenses.id = ".$char."_additional_informations".$additional_informations_counter.".reference_id ";
		    	}

		    	
		    	
		    }
	    }
    	
    	$expense_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    (
		        SELECT
		        	id,
		        	deleted,
	            province,
	            sub_categories_id,
	            expense_date,
	            expense_amount,
	            currency,
	            description
		        FROM expenses
			    ) AS expenses
					$variable_portion
				WHERE
					expenses.deleted='0'
					AND expenses.province=:province
					AND expenses.sub_categories_id=:sub_categories_id
					AND expenses.expense_date=:expense_date
					AND expenses.expense_amount=:expense_amount
					AND expenses.currency=:currency
					AND expenses.description=:description
					AND expenses.id!=:expenses_id
					$condition_portion
				  "
			);

			$expense_sqx = $expense_sq->execute([
		  	'province'=>$province,
		  	'sub_categories_id'=>$sub_categories_id,
		  	'expense_date'=>$expense_date,
		  	'expense_amount'=>$expense_amount,
		  	'currency'=>$currency,
		  	'description'=>$description,
		  	'expenses_id'=>$expenses_id
		  ]);

    }else{

    	if(sizeof($key_infos)>0){
	    	for ($i=0; $i<sizeof($key_infos); $i++) {
		
		    	$key_info = holu_escape($key_infos[$i]);
		    	$value_info = holu_escape($value_infos[$i]);

		    	if($key_info!=""){
		    		switch ($key_info) {

			    		case 'Customer Name':{
					      $char = "customer_name";
					    }break;
					    case 'Customer ID':{
					      $char = "customer_id";
					    }break;
					    case 'Package':{
					      $char = "package";
					    }break;
					    case 'Start Date':{
					      $char = "start_date";
					    }break;
					    case 'End Date':{
					      $char = "end_date";
					    }break;
					    case 'Equipment':{
					    	$char = "equipment";
					    }break;
					    case 'Other Services':{
					    	$char = "other_services";
					    }break;
					    default:{
					    	$char = "default";
					    }break;
			    	}

				    $variable_portion .= " ,
					    (
				        SELECT
			            reference_id,
			            value_info AS ".$char."
				        FROM
			            additional_informations
				        WHERE
			            reference_type = 'Expense' 
			            AND key_info = '".$key_info."'
			            AND value_info = '".$value_info."'
					    ) AS ".$char."_additional_informations"; 

						$condition_portion .= " AND expenses.id = ".$char."_additional_informations.reference_id ";
		    	}

		    	
		    	
		    }
	    }

    	$expense_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    (
		        SELECT
		        	id,
		        	deleted,
	            province,
	            sub_categories_id,
	            expense_date,
	            expense_amount,
	            currency,
	            description
		        FROM expenses
			    ) AS expenses
					$variable_portion
				WHERE
					expenses.deleted='0'
					AND expenses.province=:province
					AND expenses.sub_categories_id=:sub_categories_id
					AND expenses.expense_date=:expense_date
					AND expenses.expense_amount=:expense_amount
					AND expenses.currency=:currency
					AND expenses.description=:description
					$condition_portion"
			);

			$expense_sqx = $expense_sq->execute([
		  	'province'=>$province,
		  	'sub_categories_id'=>$sub_categories_id,
		  	'expense_date'=>$expense_date,
		  	'expense_amount'=>$expense_amount,
		  	'currency'=>$currency,
		  	'description'=>$description
		  ]);

    }

		



	  if($expense_sq->rowCount()>0){
	  	$flag = 1;
	  }

	  return $flag;
	}

	function check_duplicate_exchange($post){
		global $db;
		$flag = 0;

		$province        = holu_escape($post['province'] ?? '');
		$exchange_date   = holu_escape($post['exchange_date'] ?? '');
		$from_amount     = holu_escape($post['from_amount'] ?? '');
		$to_amount       = holu_escape($post['to_amount'] ?? '');
		$from_currency   = holu_escape($post['from_currency'] ?? '');
		$to_currency     = holu_escape($post['to_currency'] ?? '');
		$description     = holu_escape($post['description'] ?? '');

    if(isset($post['data_id']) AND !empty($post['data_id'])){
    	$exchanges_id = holu_escape(holu_decode($post['data_id']));

    	
    	
    	$exchange_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    `exchanges`
				WHERE
					exchanges.deleted='0'
					AND exchanges.province=:province
					AND exchanges.exchange_date=:exchange_date
					AND exchanges.from_amount=:from_amount
					AND exchanges.to_amount=:to_amount
					AND exchanges.from_currency=:from_currency
					AND exchanges.to_currency=:to_currency
					AND exchanges.description=:description
					AND exchanges.id!=:exchanges_id
				"
			);

			$exchange_sqx = $exchange_sq->execute([
		  	'province'=>$province,
		  	'exchange_date'=>$exchange_date,
		  	'from_amount'=>$from_amount,
		  	'to_amount'=>$to_amount,
		  	'from_currency'=>$from_currency,
		  	'to_currency'=>$to_currency,
		  	'description'=>$description,
		  	'exchanges_id'=>$exchanges_id
		  ]);

    }else{

    	

    	$exchange_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    `exchanges`
				WHERE
					exchanges.deleted='0'
					AND exchanges.province=:province
					AND exchanges.exchange_date=:exchange_date
					AND exchanges.from_amount=:from_amount
					AND exchanges.to_amount=:to_amount
					AND exchanges.from_currency=:from_currency
					AND exchanges.to_currency=:to_currency
					AND exchanges.description=:description
				"
			);

			$exchange_sqx = $exchange_sq->execute([
		  	'province'=>$province,
		  	'exchange_date'=>$exchange_date,
		  	'from_amount'=>$from_amount,
		  	'to_amount'=>$to_amount,
		  	'from_currency'=>$from_currency,
		  	'to_currency'=>$to_currency,
		  	'description'=>$description
		  ]);

    }

		



	  if($exchange_sq->rowCount()>0){
	  	$flag = 1;
	  }

	  return $flag;
	}

	function check_duplicate_purchase($post){
		global $db;
		$flag = 0;

		$logistic_cashes_id = holu_escape($post['logistic_cashes_id']);
		$province = holu_escape($post['province']);
    $purchase_date = holu_escape($post['purchase_date']);
		$purchase_amount = holu_escape($post['purchase_amount']);
    $currency = holu_escape($post['currency']);
    $description = holu_escape($post['description']);

    if(isset($post['data_id']) AND !empty($post['data_id'])){
    	$purchases_id = holu_escape(holu_decode($post['data_id']));

    	
    	
    	$purchase_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    `purchases`
				WHERE
					purchases.deleted='0'
					AND purchases.logistic_cashes_id=:logistic_cashes_id
					AND purchases.province=:province
					AND purchases.purchase_date=:purchase_date
					AND purchases.purchase_amount=:purchase_amount
					AND purchases.currency=:currency
					AND purchases.description=:description
					AND purchases.id!=:purchases_id
				"
			);

			$purchase_sqx = $purchase_sq->execute([
		  	'logistic_cashes_id'=>$logistic_cashes_id,
		  	'province'=>$province,
		  	'purchase_date'=>$purchase_date,
		  	'purchase_amount'=>$purchase_amount,
		  	'currency'=>$currency,
		  	'description'=>$description,
		  	'purchases_id'=>$purchases_id
		  ]);

    }else{

    	

    	$purchase_sq = $db->prepare(
				"SELECT
				  *
				FROM
			    `purchases`
				WHERE
					purchases.deleted='0'
					AND purchases.logistic_cashes_id=:logistic_cashes_id
					AND purchases.province=:province
					AND purchases.purchase_date=:purchase_date
					AND purchases.purchase_amount=:purchase_amount
					AND purchases.currency=:currency
					AND purchases.description=:description
				"
			);

			$purchase_sqx = $purchase_sq->execute([
		  	'logistic_cashes_id'=>$logistic_cashes_id,
		  	'province'=>$province,
		  	'purchase_date'=>$purchase_date,
		  	'purchase_amount'=>$purchase_amount,
		  	'currency'=>$currency,
		  	'description'=>$description
		  ]);

    }

		



	  if($purchase_sq->rowCount()>0){
	  	$flag = 1;
	  }

	  return $flag;
	}

	function track_editions($operation_type, $operation_array){

		global $db;
		global $holu_date;
		global $holu_time;
		global $holu_users_id;

		switch ($operation_type) {
			case 'edit_income':{

				$old_data = "";
				$new_data = "";

				$incomes_id = holu_escape(holu_decode($operation_array['incomes_id']));

				$old_data .= '`Province`=>`'.get_col('incomes', 'province', 'id', $incomes_id).'`###';
				$old_data .= '`Sub Category`=>`'.get_col('incomes', 'sub_categories_id', 'id', $incomes_id).'`###';
				$old_data .= '`Income Date`=>`'.get_col('incomes', 'income_date', 'id', $incomes_id).'`###';
				$old_data .= '`Income Amount`=>`'.get_col('incomes', 'income_amount', 'id', $incomes_id).'`###';
				$old_data .= '`Currency`=>`'.get_col('incomes', 'currency', 'id', $incomes_id).'`###';
				$old_data .= '`Description`=>`'.get_col('incomes', 'description', 'id', $incomes_id).'`###';

				$additional_information_sq = $db->prepare(
					"SELECT key_info, value_info
					FROM `additional_informations`
					WHERE reference_type='Income'
					AND reference_id=:incomes_id
					AND deleted='0'"
				);

				$additional_information_sqx = $additional_information_sq->execute([
					'incomes_id'=>$incomes_id
				]);

				$additional_informations = '';
				if($additional_information_sq->rowCount()>0){
					while ($additional_information_row = $additional_information_sq->fetch()) {
						$old_data .= '`'.$additional_information_row['key_info'].'`=>`'.$additional_information_row['value_info'].'`###';
					}
				}


				$new_data .= '`Province`=>`'.holu_escape($operation_array['data_array']['province']).'`###';
		    $new_data .= '`Sub Category`=>`'.holu_escape($operation_array['data_array']['sub_categories_id']).'`###';
		    $new_data .= '`Income Date`=>`'.holu_escape($operation_array['data_array']['income_date']).'`###';
				$new_data .= '`Income Amount`=>`'.holu_escape($operation_array['data_array']['income_amount']).'`###';
		    $new_data .= '`Currency`=>`'.holu_escape($operation_array['data_array']['currency']).'`###';
		    $new_data .= '`Description`=>`'.holu_escape($operation_array['data_array']['description']).'`###';

		    

		    $key_infos = array();
		    $value_infos = array();

		    if(!empty($_POST['customer_name'])){
		    	array_push($key_infos, 'Customer Name');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['customer_name']));
		    }
		    if(!empty($_POST['customer_id'])){
		    	array_push($key_infos, 'Customer ID');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['customer_id']));
		    }
		    if(!empty($_POST['package'])){
		    	array_push($key_infos, 'Package');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['package']));
		    }
		    if(!empty($_POST['start_date'])){
		    	array_push($key_infos, 'Start Date');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['start_date']));
		    }
		    if(!empty($_POST['end_date'])){
		    	array_push($key_infos, 'End Date');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['end_date']));
		    }
		    if(!empty($_POST['equipment'])){
		    	array_push($key_infos, 'Equipment');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['equipment']));
		    }
		    if(!empty($_POST['other_services'])){
		    	array_push($key_infos, 'Other Services');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['other_services']));
		    }
		    if(!empty($_POST['employee'])){
		    	array_push($key_infos, 'Employee');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['employee']));
		    }
		    

		    if(sizeof($key_infos)>0){
	    		for ($i=0; $i<sizeof($key_infos); $i++) {
		    		$new_data .= '`'.holu_escape($key_infos[$i]).'`=>`'.holu_escape($value_infos[$i]).'`###';
	    		}
	    	}

	    	$transaction_edition_iq = $db->prepare(
	    		"INSERT INTO `transaction_editions` (
	    			reference_type,
	    			reference_id,
	    			old_data,
	    			new_data,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id, 
			    	:old_data, 
			    	:new_data, 
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_edition_iqx = $transaction_edition_iq->execute([
		    	'reference_type'=>'Income',
		    	'reference_id'=>$incomes_id,
		    	'old_data'=>$old_data,
		    	'new_data'=>$new_data,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);


			}break;

			case 'edit_expense':{

				$old_data = "";
				$new_data = "";

				$expenses_id = holu_escape(holu_decode($operation_array['expenses_id']));

				$old_data .= '`Province`=>`'.get_col('expenses', 'province', 'id', $expenses_id).'`###';
				$old_data .= '`Sub Category`=>`'.get_col('expenses', 'sub_categories_id', 'id', $expenses_id).'`###';
				$old_data .= '`Expense Date`=>`'.get_col('expenses', 'expense_date', 'id', $expenses_id).'`###';
				$old_data .= '`Expense Amount`=>`'.get_col('expenses', 'expense_amount', 'id', $expenses_id).'`###';
				$old_data .= '`Currency`=>`'.get_col('expenses', 'currency', 'id', $expenses_id).'`###';
				$old_data .= '`Description`=>`'.get_col('expenses', 'description', 'id', $expenses_id).'`###';

				$additional_information_sq = $db->prepare(
					"SELECT key_info, value_info
					FROM `additional_informations`
					WHERE reference_type='Expense'
					AND reference_id=:expenses_id
					AND deleted='0'"
				);

				$additional_information_sqx = $additional_information_sq->execute([
					'expenses_id'=>$expenses_id
				]);

				$additional_informations = '';
				if($additional_information_sq->rowCount()>0){
					while ($additional_information_row = $additional_information_sq->fetch()) {
						$old_data .= '`'.$additional_information_row['key_info'].'`=>`'.$additional_information_row['value_info'].'`###';
					}
				}

				


				$new_data .= '`Province`=>`'.holu_escape($operation_array['data_array']['province']).'`###';
		    $new_data .= '`Sub Category`=>`'.holu_escape($operation_array['data_array']['sub_categories_id']).'`###';
		    $new_data .= '`Expense Date`=>`'.holu_escape($operation_array['data_array']['expense_date']).'`###';
				$new_data .= '`Expense Amount`=>`'.holu_escape($operation_array['data_array']['expense_amount']).'`###';
		    $new_data .= '`Currency`=>`'.holu_escape($operation_array['data_array']['currency']).'`###';
		    $new_data .= '`Description`=>`'.holu_escape($operation_array['data_array']['description']).'`###';

		    $key_infos = array();
		    $value_infos = array();

		    if(!empty($_POST['customer_name'])){
		    	array_push($key_infos, 'Customer Name');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['customer_name']));
		    }
		    if(!empty($_POST['customer_id'])){
		    	array_push($key_infos, 'Customer ID');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['customer_id']));
		    }
		    if(!empty($_POST['package'])){
		    	array_push($key_infos, 'Package');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['package']));
		    }
		    if(!empty($_POST['start_date'])){
		    	array_push($key_infos, 'Start Date');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['start_date']));
		    }
		    if(!empty($_POST['end_date'])){
		    	array_push($key_infos, 'End Date');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['end_date']));
		    }
		    if(!empty($_POST['equipment'])){
		    	array_push($key_infos, 'Equipment');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['equipment']));
		    }
		    if(!empty($_POST['other_services'])){
		    	array_push($key_infos, 'Other Services');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['other_services']));
		    }
		    if(!empty($_POST['employee'])){
		    	array_push($key_infos, 'Employee');
		    	array_push($value_infos, holu_escape($operation_array['data_array']['employee']));
		    }
		    

		    if(sizeof($key_infos)>0){
	    		for ($i=0; $i<sizeof($key_infos); $i++) {
		    		$new_data .= '`'.holu_escape($key_infos[$i]).'`=>`'.holu_escape($value_infos[$i]).'`###';
	    		}
	    	}

		    

	    	$transaction_edition_iq = $db->prepare(
	    		"INSERT INTO `transaction_editions` (
	    			reference_type,
	    			reference_id,
	    			old_data,
	    			new_data,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id, 
			    	:old_data, 
			    	:new_data, 
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_edition_iqx = $transaction_edition_iq->execute([
		    	'reference_type'=>'Expense',
		    	'reference_id'=>$expenses_id,
		    	'old_data'=>$old_data,
		    	'new_data'=>$new_data,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);


			}break;

			case 'edit_exchange':{

				$old_data = "";
				$new_data = "";

				$exchanges_id = holu_escape(holu_decode($operation_array['exchanges_id']));

				$old_data .= '`Province`=>`'.get_col('exchanges', 'province', 'id', $exchanges_id).'`###';
				$old_data .= '`Exchange Date`=>`'.get_col('exchanges', 'exchange_date', 'id', $exchanges_id).'`###';
				$old_data .= '`From Amount`=>`'.get_col('exchanges', 'from_amount', 'id', $exchanges_id).'`###';
				$old_data .= '`To Amount`=>`'.get_col('exchanges', 'to_amount', 'id', $exchanges_id).'`###';
				$old_data .= '`From Currency`=>`'.get_col('exchanges', 'from_currency', 'id', $exchanges_id).'`###';
				$old_data .= '`To Currency`=>`'.get_col('exchanges', 'to_currency', 'id', $exchanges_id).'`###';
				$old_data .= '`Description`=>`'.get_col('exchanges', 'description', 'id', $exchanges_id).'`###';


				$new_data .= '`Province`=>`'.holu_escape($operation_array['data_array']['province'] ?? '').'`###';
				$new_data .= '`Exchange Date`=>`'.holu_escape($operation_array['data_array']['exchange_date'] ?? '').'`###';
				$new_data .= '`From Amount`=>`'.holu_escape($operation_array['data_array']['from_amount'] ?? '').'`###';
				$new_data .= '`To Amount`=>`'.holu_escape($operation_array['data_array']['to_amount'] ?? '').'`###';
				$new_data .= '`From Currency`=>`'.holu_escape($operation_array['data_array']['from_currency'] ?? '').'`###';
				$new_data .= '`To Currency`=>`'.holu_escape($operation_array['data_array']['to_currency'] ?? '').'`###';
				$new_data .= '`Description`=>`'.holu_escape($operation_array['data_array']['description'] ?? '').'`###';

	    	$transaction_edition_iq = $db->prepare(
	    		"INSERT INTO `transaction_editions` (
	    			reference_type,
	    			reference_id,
	    			old_data,
	    			new_data,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id, 
			    	:old_data, 
			    	:new_data, 
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_edition_iqx = $transaction_edition_iq->execute([
		    	'reference_type'=>'Exchange',
		    	'reference_id'=>$exchanges_id,
		    	'old_data'=>$old_data,
		    	'new_data'=>$new_data,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);


			}break;

			case 'edit_purchase':{

				$old_data = "";
				$new_data = "";

				$purchases_id = holu_escape(holu_decode($operation_array['purchases_id']));

				$old_data .= '`Logistic Cash`=>`'.get_col('purchases', 'logistic_cashes_id', 'id', $purchases_id).'`###';
				$old_data .= '`Province`=>`'.get_col('purchases', 'province', 'id', $purchases_id).'`###';
				$old_data .= '`Sub Category`=>`'.get_col('purchases', 'sub_categories_id', 'id', $purchases_id).'`###';
				$old_data .= '`Purchase Date`=>`'.get_col('purchases', 'purchase_date', 'id', $purchases_id).'`###';
				$old_data .= '`Purchase Amount`=>`'.get_col('purchases', 'purchase_amount', 'id', $purchases_id).'`###';
				$old_data .= '`Currency`=>`'.get_col('purchases', 'currency', 'id', $purchases_id).'`###';
				$old_data .= '`Description`=>`'.get_col('purchases', 'description', 'id', $purchases_id).'`###';


				$new_data .= '`Logistic Cash`=>`'.holu_escape($operation_array['data_array']['logistic_cashes_id']).'`###';
				$new_data .= '`Province`=>`'.holu_escape($operation_array['data_array']['province']).'`###';
				$new_data .= '`Sub Category`=>`'.holu_escape($operation_array['data_array']['sub_categories_id']).'`###';
		    $new_data .= '`Purchase Date`=>`'.holu_escape($operation_array['data_array']['purchase_date']).'`###';
				$new_data .= '`Purchase Amount`=>`'.holu_escape($operation_array['data_array']['purchase_amount']).'`###';
		    $new_data .= '`Currency`=>`'.holu_escape($operation_array['data_array']['currency']).'`###';
		    $new_data .= '`Description`=>`'.holu_escape($operation_array['data_array']['description']).'`###';

	    	$transaction_edition_iq = $db->prepare(
	    		"INSERT INTO `transaction_editions` (
	    			reference_type,
	    			reference_id,
	    			old_data,
	    			new_data,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id, 
			    	:old_data, 
			    	:new_data, 
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_edition_iqx = $transaction_edition_iq->execute([
		    	'reference_type'=>'Purchase',
		    	'reference_id'=>$purchases_id,
		    	'old_data'=>$old_data,
		    	'new_data'=>$new_data,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);


			}break;

			case 'edit_transfer':{



				$old_data = "";
				$new_data = "";

				$transfers_id = holu_escape(holu_decode($operation_array['transfers_id']));

				$old_data .= '`From Province`=>`'.get_col('transfers', 'from_province', 'id', $transfers_id).'`###';
				$old_data .= '`To Province`=>`'.get_col('transfers', 'to_province', 'id', $transfers_id).'`###';
				$old_data .= '`Transfer Date`=>`'.get_col('transfers', 'transfer_date', 'id', $transfers_id).'`###';
				$old_data .= '`Amount`=>`'.get_col('transfers', 'transfer_amount', 'id', $transfers_id).'`###';
				$old_data .= '`Currency`=>`'.get_col('transfers', 'currency', 'id', $transfers_id).'`###';
				$old_data .= '`Description`=>`'.get_col('transfers', 'description', 'id', $transfers_id).'`###';


				$new_data .= '`From Province`=>`'.holu_escape($operation_array['data_array']['from_province']).'`###';
				$new_data .= '`To Province`=>`'.holu_escape($operation_array['data_array']['to_province']).'`###';
		    $new_data .= '`Transfer Date`=>`'.holu_escape($operation_array['data_array']['transfer_date']).'`###';
				$new_data .= '`Amount`=>`'.holu_escape($operation_array['data_array']['transfer_amount']).'`###';
		    $new_data .= '`Currency`=>`'.holu_escape($operation_array['data_array']['currency']).'`###';
		    $new_data .= '`Description`=>`'.holu_escape($operation_array['data_array']['description']).'`###';

	    	$transaction_edition_iq = $db->prepare(
	    		"INSERT INTO `transaction_editions` (
	    			reference_type,
	    			reference_id,
	    			old_data,
	    			new_data,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id, 
			    	:old_data, 
			    	:new_data, 
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_edition_iqx = $transaction_edition_iq->execute([
		    	'reference_type'=>'Transfer',
		    	'reference_id'=>$transfers_id,
		    	'old_data'=>$old_data,
		    	'new_data'=>$new_data,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);


			}break;
			
			default:{

			}break;
		}

    	
    

    
	}

	function track_deletions($operation_type, $operation_array){

		global $db;
		global $holu_date;
		global $holu_time;
		global $holu_users_id;

		switch ($operation_type) {
			case 'delete_income':{

				$incomes_id = holu_escape(holu_decode($operation_array['incomes_id']));

				$transaction_deletion_iq = $db->prepare(
	    		"INSERT INTO `transaction_deletions` (
	    			reference_type,
	    			reference_id,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id,
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_deletion_iqx = $transaction_deletion_iq->execute([
		    	'reference_type'=>'Income',
		    	'reference_id'=>$incomes_id,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);

			}break;

			case 'delete_expense':{

				$expenses_id = holu_escape(holu_decode($operation_array['expenses_id']));

				$transaction_deletion_iq = $db->prepare(
	    		"INSERT INTO `transaction_deletions` (
	    			reference_type,
	    			reference_id,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id,
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_deletion_iqx = $transaction_deletion_iq->execute([
		    	'reference_type'=>'Expense',
		    	'reference_id'=>$expenses_id,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);

			}break;

			case 'delete_exchange':{

				$exchanges_id = holu_escape(holu_decode($operation_array['exchanges_id']));

				$transaction_deletion_iq = $db->prepare(
	    		"INSERT INTO `transaction_deletions` (
	    			reference_type,
	    			reference_id,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id,
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_deletion_iqx = $transaction_deletion_iq->execute([
		    	'reference_type'=>'Exchange',
		    	'reference_id'=>$exchanges_id,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);

			}break;

			case 'delete_purchase':{

				$purchases_id = holu_escape(holu_decode($operation_array['purchases_id']));

				$transaction_deletion_iq = $db->prepare(
	    		"INSERT INTO `transaction_deletions` (
	    			reference_type,
	    			reference_id,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id,
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_deletion_iqx = $transaction_deletion_iq->execute([
		    	'reference_type'=>'Purchase',
		    	'reference_id'=>$purchases_id,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);

			}break;

			case 'delete_transfer':{

				$transfers_id = holu_escape(holu_decode($operation_array['transfers_id']));

				$transaction_deletion_iq = $db->prepare(
	    		"INSERT INTO `transaction_deletions` (
	    			reference_type,
	    			reference_id,
	    			insertion_date, 
				    insertion_time, 
				    users_id
		    	) VALUES (
		    		:reference_type, 
			    	:reference_id,
			    	:holu_date, 
			    	:holu_time,
			    	:holu_users_id
		    	)"
	    	);

	    	$transaction_deletion_iqx = $transaction_deletion_iq->execute([
		    	'reference_type'=>'Transfer',
		    	'reference_id'=>$transfers_id,
		    	'holu_date'=>$holu_date,
		    	'holu_time'=>$holu_time,
		    	'holu_users_id'=>$holu_users_id
		    ]);

			}break;
			
			default:{

			}break;
		}

	}

	function check_in_use($type, $data_array){

		global $db;
		$result = 0;

		switch ($type) {
			case 'sub_category_in_transaction':{

				$sub_categories_id = $data_array['sub_categories_id'];
				$num_transaction = 0;

				$income_sq = $db->prepare(
					"SELECT count(id) as num_income
					FROM `incomes`
					WHERE deleted = '0'
					AND sub_categories_id=:sub_categories_id
					LIMIT 1"
				);

				$income_sqx = $income_sq->execute([
					'sub_categories_id'=>$sub_categories_id
				]);

				if($income_sq->rowCount()>0){
					$income_row = $income_sq->fetch();
					$num_transaction += $income_row['num_income'];
				}

				$expense_sq = $db->prepare(
					"SELECT count(id) as num_expense
					FROM `expenses`
					WHERE deleted = '0'
					AND sub_categories_id=:sub_categories_id
					LIMIT 1"
				);

				$expense_sqx = $expense_sq->execute([
					'sub_categories_id'=>$sub_categories_id
				]);

				if($expense_sq->rowCount()>0){
					$expense_row = $expense_sq->fetch();
					$num_transaction += $expense_row['num_expense'];
				}

				if($num_transaction>0){
					$result = 1;
				}



			}break;

			case 'category_in_sub_category':{

				$categories_id = $data_array['categories_id'];
				$num_sub_category = 0;

				$sub_category_sq = $db->prepare(
					"SELECT count(id) as num_sub_category
					FROM `sub_categories`
					WHERE deleted = '0'
					AND categories_id=:categories_id
					LIMIT 1"
				);

				$sub_category_sqx = $sub_category_sq->execute([
					'categories_id'=>$categories_id
				]);

				if($sub_category_sq->rowCount()>0){
					$sub_category_row = $sub_category_sq->fetch();
					$num_sub_category += $sub_category_row['num_sub_category'];
				}

				if($num_sub_category>0){
					$result = 1;
				}

			}break;
			
			default:{

			}break;
		}

		return $result;

	}


	function get_additional_information_items($additional_information_items){
		global $db;
		$result = '';

		$additional_information_items_sq = $db->prepare(
			"SELECT *
			FROM `additional_information_items`
			WHERE deleted = '0'"
		);

		$additional_information_items_sqx = $additional_information_items_sq->execute();

		if($additional_information_items_sq->rowCount()>0){
			while($additional_information_items_row = $additional_information_items_sq->fetch()){

				if(in_array($additional_information_items_row['id'], $additional_information_items)){
					$checked = 'checked';
				}else{
					$checked = '';
				}

				$result .= '
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="additional_information_items[]" id="'.$additional_information_items_row['name'].'" value="'.$additional_information_items_row['id'].'" '.$checked.'>
					  <label class="form-check-label" for="'.$additional_information_items_row['name'].'">
					    '.$additional_information_items_row['label'].'
					  </label>
					</div>
				';

			}
		}

		return $result;

	}

	function get_ai_input($selector, $selector_value, $default_value){
		$result = '';
		$ai_items_id = get_col('additional_information_items', 'id', $selector, $selector_value);

		switch($ai_items_id){
			case '1':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="customer_name">Customer Name</label>
          <div class="col-sm-6">
            <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Type here..." required value="'.$default_value.'" onkeyup="suggest_data(this, \'additional_information_customer_name\');" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '2':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="customer_id">Customer ID</label>
          <div class="col-sm-6">
            <input type="text" 
                   id="customer_id" 
                   name="customer_id" 
                   class="form-control" 
                   placeholder="Type here..." 
                   autocomplete="off"
                   required value="'.$default_value.'">
          </div>
        </div>
        ';

			}break;

			case '3':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="package">Package</label>
          <div class="col-sm-6">
            <input type="text" id="package" name="package" class="form-control" placeholder="Type here..." required value="'.$default_value.'" onkeyup="suggest_data(this, \'additional_information_package\');" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '4':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="start_date">Start Date</label>
          <div class="col-sm-6">
            <input type="text" id="start_date" name="start_date" class="form-control date_picker" placeholder="Type here..." required value="'.$default_value.'" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '5':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="end_date">End Date</label>
          <div class="col-sm-6">
            <input type="text" id="end_date" name="end_date" class="form-control date_picker" placeholder="Type here..." required value="'.$default_value.'" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '6':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="equipment">Equipment</label>
          <div class="col-sm-6">
            <input type="text" id="equipment" name="equipment" class="form-control" placeholder="Type here..." required value="'.$default_value.'" onkeyup="suggest_data(this, \'additional_information_equipment\');" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '7':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="other_services">Other Services</label>
          <div class="col-sm-6">
            <input type="text" id="other_services" name="other_services" class="form-control" placeholder="Type here..." required value="'.$default_value.'" onkeyup="suggest_data(this, \'additional_information_other_services\');" autocomplete="off">
          </div>
        </div>
        ';

			}break;

			case '8':{
				$result .= '
				<div class="form-group row">
          <label class="col-sm-3 col-form-label" for="employee">Employee</label>
          <div class="col-sm-6">
            <select id="employee" name="employee" class="form-control select2" required>
              <option selected hidden value="">Select an option</option>
              '.get_employee_option($default_value).'
            </select>
          </div>
        </div>
        ';

			}break;

			default:{
				$result .= '';
			}break;
		}

		return $result;
	
	}

	function print_ai_labels($ai){

		$additional_informations = '';

    if(!empty($ai)){
      foreach ($ai as $key => $value) {
      	if($key=='Employee'){

      		global $db;

      		$employee_sq = $db->query("SELECT id, first_name, last_name, id_number FROM hr_employees WHERE id='$value' LIMIT 1");
      		$employee_row = $employee_sq->fetch();
      		$value = $employee_row['last_name'].'.'.$employee_row['id_number'];
      	}
        $additional_informations .= '
          <div class="badge badge-secondary">
            '.$key.': '.$value.'
          </div>
        ';
      }
    }

    return $additional_informations;

	}

	function get_year_options($year){

    $years = array(
      '2015',
      '2016',
      '2017',
      '2018',
      '2019',
      '2020',
      '2021',
      '2022',
      '2023',
      '2024',
      '2025',
    );

    $year_options = '';
    if (count($years)>0){
      foreach ($years as $year_name){

        $year_options .= '<option '.($year_name==$year?'selected':'').' value="'.$year_name.'">'.$year_name.'</option>';

      }
    }

    return $year_options;

  }

	function get_month_options($month){

    $months = array(
      ['January', '01'],
      ['February', '02'],
      ['March', '03'],
      ['April', '04'],
      ['May', '05'],
      ['June', '06'],
      ['July', '07'],
      ['August', '08'],
      ['September', '09'],
      ['October', '10'],
      ['November', '11'],
      ['December', '12'],
    );

    $month_options = '';
    if (count($months)>0){
      foreach ($months as $month_name){

        $month_options .= '<option '.($month_name[1]==$month?'selected':'').' value="'.$month_name[1].'">'.$month_name[0].'</option>';

      }
    }

    return $month_options;

  }

	//End of functions

	$accessed_provinces = set_province_portion();
	
	$accessed_sub_categories_income = set_sub_category_portion('Income');

	$accessed_sub_categories_expense = set_sub_category_portion('Expense');

	$accessed_sub_categories_purchase = set_sub_category_portion('Purchase');

	if(check_access('sub_category_accessibility/exchange/')==1){
		$accessed_sub_categories_exchange = "";
	}else{
		$accessed_sub_categories_exchange = " AND 0 ";
	}

	if(check_access('sub_category_accessibility/transfer/')==1){
		$accessed_sub_categories_transfer = "";
	}else{
		$accessed_sub_categories_transfer = " AND 0 ";
	}

	$accessed_logistic_cashes = set_logistic_cash_portion();
	
	escape_url_injection($holu_portions);

	

}else{
	header("location:../login.php");
}

?>
