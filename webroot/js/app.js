var app = angular.module('blog', ['ngResource']);
app.controller('MainController', ['Post', function(Post) {

    this.title = 'Stream';
    this.posts = Post.query();
    this.loading = false;

    var self = this;
    this.save = function(post) {
        this.loading = true;
        post.$save(function(res) {
            this.loading = false;
        }, function(res) {
            this.loading = false;
            console.error(res);
        });
    };

    this.delete = function(post) {
        post.$delete(function(res) {
            self.posts = self.posts.filter(function(el) {
                return el.id !== res.id;
            });
        }, function(res) {
            console.log(res);
        });
    };
}]);

app.factory('Post', ['$resource', function($resource) {
    return $resource('/posts/:id.json', {id: "@id"}, {
        query: {
            method: 'GET',
            isArray: true
        },
        delete: {
            method: 'DELETE'
        }
    });
}]);
