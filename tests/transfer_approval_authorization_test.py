from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
controller = (ROOT / "www" / "controller_transfer.php").read_text()
configuration = (ROOT / "lib" / "_configuration.php").read_text()
report = (ROOT / "www" / "report_transfer.php").read_text()


def test_create_transfer_destination_options_are_unrestricted():
    add_form = controller[controller.index('case "add_transfer_form"'):controller.index('case "edit_transfer_form"')]
    add_operation = controller[controller.index('case "add_transfer"'):controller.index('case "edit_transfer"')]
    assert 'id="to_province"' in add_form
    assert 'get_all_province_option("0")' in add_form
    assert 'to_province IN ($accessed_provinces)' not in add_operation
    assert 'transfer_source_is_accessible_to_user' not in add_operation


def test_approve_transfer_uses_source_branch_permission_not_destination():
    approve_operation = controller[controller.index('case "approve_transfer"'):controller.index('case "delete_transfer"')]
    assert 'transfer_source_is_accessible_to_user($data_id)' in approve_operation
    assert 'to_province IN ($accessed_provinces)' not in approve_operation


def test_source_permission_helper_checks_from_province_and_from_branch():
    assert 'function transfer_source_is_accessible_to_user($transfer_id)' in configuration
    assert "set_province_branch_portion('from_province', 'from_branch')" in configuration
    assert 'transfer_source_is_accessible_to_user($transfer_row[\'id\'])' in report
    assert "set_province_branch_portion('from_province', 'from_branch')" in report
