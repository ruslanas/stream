describe('Tasks Tab', function() {

    beforeEach(function() {
        browser.get('http://localhost:9001/tasks');
    });

    it('needs to login', function() {

        var l = element(by.linkText('Sign In')).click();

        element(by.id('email')).sendKeys('protractor@example.com');
        element(by.id('password')).sendKeys('default');

        element(by.id('sign-in-btn')).click();

        var mis = element.all(by.css('#menu .ng-hide'));
        expect(mis.count()).toEqual(2);

    });

    it('should add a task', function() {

        var message = "Add a task";

        var mi = element(by.id('title'));

        mi.sendKeys(message);
        
        element(by.id('description')).sendKeys("and description");

        element(by.id('save-btn')).click();

        var item = element(by.css('.panel:nth-child(1)'));

        expect(item.getText()).toContain(message);

    });

    it("should delegate", function() {


        element(by.linkText('Tasks')).click();

        var task = element(by.className('panel-primary'));

        task.element(by.css('.input-xs')).sendKeys("admin@stream.wri.lt");

        task.element(by.css('.btn-info')).click();

        expect(element(by.className('panel-info')).getText()).toContain('admin@stream.wri.lt');

    });

    it('should dismiss a task', function() {

        element(by.css('.panel:not(.panel-primary) .btn-danger')).click();

        expect(element(by.css('.alert-info > div')).getText()).toEqual('Task dismissed');
        element(by.linkText('Sign Out')).click();
        expect(element(by.css('.btn-primary')).getText()).toEqual('Sign In');

    });

});
