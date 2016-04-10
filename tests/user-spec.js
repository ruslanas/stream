describe('New User', function() {

    beforeEach(function() {
        browser.get('http://localhost:9001');
    });

    it('should register', function() {

        // random users are ok
        var email = parseInt(Math.random() * 10000) + '_protractor@example.com';
        var password = 'default';

        element(by.linkText('Register')).click();

        element(by.id('email')).sendKeys(email);

        element(by.id('password')).sendKeys(password);
        element(by.id('password2')).sendKeys(password);

        element(by.id('register-btn')).click();

        expect(element.all(by.id('sign-in-btn')).count()).toBe(1);

        expect(element(by.id('email')).getAttribute('value')).toEqual(email);

    });

    it('should provide validation errors', function() {
    
        var email = 'email@email';
        var password = 'password';
    
        element(by.linkText('Register')).click();
        element(by.css('[type=email]')).sendKeys(email);
        element(by.css('[type=password]')).sendKeys(password);
        element(by.css('.btn-primary')).click();

        expect(element(by.css('.help-block')).getText()).toContain('Email invalid');
    
    });

});
