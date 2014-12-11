<?php
namespace EllisLab\Tests\ExpressionEngine\Service;

use EllisLab\ExpressionEngine\Service\Filter\Username;
use Mockery as m;
use \stdClass;

class UsernameTest extends \PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$this->query = m::mock('EllisLab\ExpressionEngine\Service\Model\Query\Builder');

		$this->usernames = array(
			'1' => 'admin',
			'2' => 'johndoe',
			'3' => 'janedoe',
			'5' => 'somebody',
			'9' => 'nobody'
		);
	}

	public function tearDown()
	{
		unset($_POST['filter_by_username']);
		unset($_GET['filter_by_username']);
	}

	public function testDefault()
	{
		$filter = new Username($this->usernames);
		$this->assertNull($filter->value(), 'The value is NULL by default.');
		$this->assertTrue($filter->isValid(), 'The default is invalid');

		$vf = m::mock('EllisLab\ExpressionEngine\Service\View\ViewFactory');
		$url = m::mock('EllisLab\ExpressionEngine\Library\CP\URL');

		$vf->shouldReceive('make->render');
		$url->shouldReceive('setQueryStringVariable', 'compile');
		$filter->render($vf, $url);
	}

	public function testPOST()
	{
		$_POST['filter_by_username'] = 2;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(2), $filter->value(), 'The value reflects the POSTed value');
		$this->assertTrue($filter->isValid(), 'POSTing a number is valid');
	}

	public function testGET()
	{
		$_GET['filter_by_username'] = 2;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(2), $filter->value(), 'The value reflects the GETed value');
		$this->assertTrue($filter->isValid(), 'GETing a number is valid');
	}

	public function testPOSTOverGET()
	{
		$_POST['filter_by_username'] = 2;
		$_GET['filter_by_username'] = 3;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(2), $filter->value(), 'Use POST over GET');
	}

	// Use GET when POST is present but "empty"
	public function testGETWhenPOSTIsEmpty()
	{
		$_POST['filter_by_username'] = '';
		$_GET['filter_by_username'] = 3;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(3), $filter->value(), 'Use GET when POST is an empty string');

		$_POST['filter_by_username'] = NULL;
		$_GET['filter_by_username'] = 3;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(3), $filter->value(), 'Use GET when POST is NULL');

		$_POST['filter_by_username'] = 0;
		$_GET['filter_by_username'] = 3;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(3), $filter->value(), 'Use GET when POST is 0');

		$_POST['filter_by_username'] = "0";
		$_GET['filter_by_username'] = 3;
		$filter = new Username($this->usernames);
		$this->assertEquals(array(3), $filter->value(), 'Use GET when POST is "0"');
	}

	// Test valid without query and input not numeric
	public function testInvalidNonNumericValue()
	{
		$_POST['filter_by_username'] = 'admin';
		$filter = new Username($this->usernames);
		$this->assertFalse($filter->isValid(), 'POSTing a string without a Query object is invalid');

		unset($_POST['filter_by_username']);
		$_GET['filter_by_username'] = 'admin';
		$filter = new Username($this->usernames);
		$this->assertFalse($filter->isValid(), 'GETing a string without a Query object is invalid');
	}

	// Test valid without query and input not in options
	public function testInvalidNumericValue()
	{
		$_POST['filter_by_username'] = '4';
		$filter = new Username($this->usernames);
		$this->assertFalse($filter->isValid(), 'POSTing an ID not in the options array is invalid');

		unset($_POST['filter_by_username']);
		$_GET['filter_by_username'] = '4';
		$filter = new Username($this->usernames);
		$this->assertFalse($filter->isValid(), 'GETing an ID not in the options array is invalid');
	}

	protected function makeFilterWithQuery()
	{
		$filter = new Username();

		$usernames = array();
		foreach ($this->usernames as $id => $username)
		{
			$user = new stdClass();
			$user->member_id = $id;
			$user->username = $username;
			$usernames[] = $user;
		}

		$this->query->shouldReceive('count')->withNoArgs()->andReturn(count($this->usernames));
		$this->query->shouldReceive('all')->withNoArgs()->andReturn($usernames);
		$filter->setQuery($this->query);
		return $filter;
	}

	public function testSetQuery()
	{
		$filter = $this->makeFilterWithQuery();
		$this->assertEquals($this->usernames, $filter->getOptions(), "setQuery should set the options");
	}

	// Test setQuery will not overwrite options
	public function testSetQueryDoesNotOverwriteSetOptions()
	{
		$filter = new Username($this->usernames);

		$this->query->shouldReceive('count')->withNoArgs()->andReturn(5);
		$filter->setQuery($this->query);
		$this->assertSame($this->usernames, $filter->getOptions(), "setQuery should leave the options alone if they are set in the constructor");
	}

	// Test setQuery will not set options when query->count > 25
	public function testSetQueryDoesNotSetOptionsWithLargeUserCount()
	{
		$filter = new Username();

		$this->query->shouldReceive('count')->withNoArgs()->andReturn(26);
		$filter->setQuery($this->query);
		$this->assertEquals(array(), $filter->getOptions(), "setQuery should leave the options alone if there are more than 25 users");
	}

	// Test value with setQuery and input is numeric
	public function testSetQueryWithNumericInput()
	{
		// Present
		$_POST['filter_by_username'] = 2;
		$filter = $this->makeFilterWithQuery();
		$this->assertEquals(array(2), $filter->value(), 'The value reflects the submitted value');
		$this->assertTrue($filter->isValid(), 'Submitting an existing user id is valid');

		// Absent
		$_POST['filter_by_username'] = 4;
		$filter = $this->makeFilterWithQuery();
		$this->assertEquals(array(4), $filter->value(), 'The value reflects the submitted value');
		$this->assertFalse($filter->isValid(), 'Submitting non-existant user id is invalid');
	}

	public function testSetQueryWithNonNumericInputAndUserPresent()
	{
		$_POST['filter_by_username'] = 'admin';
		$filter = $this->makeFilterWithQuery();

		$members = m::mock('EllisLab\ExpressionEngine\Service\Model\Collection');
		$this->query->shouldReceive("filter->all")->andReturn($members);
		$members->shouldReceive('count')->withNoArgs()->andReturn(1);
		$members->shouldReceive('pluck')->with('member_id')->andReturn(array(1));

		$this->assertEquals(array(1), $filter->value(), 'The value reflects the id of the username');
		$this->assertTrue($filter->isValid(), 'Submitting an existing username is valid');
	}

	public function testSetQueryWithNonNumericInputAndUserNotPresent()
	{
		$_POST['filter_by_username'] = 'ferdinand.von.zeppelin';
		$filter = $this->makeFilterWithQuery();

		$members = m::mock('EllisLab\ExpressionEngine\Service\Model\Collection');
		$this->query->shouldReceive("filter->all")->andReturn($members);
		$members->shouldReceive('count')->withNoArgs()->andReturn(0);

		$this->assertEquals(array(-1), $filter->value(), 'We should have an array of -1 for failed searches');
		$this->assertFalse($filter->isValid(), 'Submitting an non-existing username is invalid');
	}

}