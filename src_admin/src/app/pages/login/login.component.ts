import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

import { IAuthorizationManager, AuthorizationManager } from './../../utils/index';

@Component({
    selector: 'login',
    styleUrls: ['./login.scss'],
    templateUrl: 'login.html'
})
export class LoginComponent implements OnInit {
    model: any = {
        username: '',
        password: '',
        isPersist: true
    };
    loading = false;
    returnUrl: string;
    errorMessage: string;

    protected authorizationManager: IAuthorizationManager;

    constructor(
        private route: ActivatedRoute,
        private router: Router,
        authorizationManager: AuthorizationManager) {
        this.authorizationManager = authorizationManager;
    }

    ngOnInit() {
        //this.authorizationManager.signOut();
        this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
    }

    login() {
        let self = this;
        self.loading = true;
        self.authorizationManager
            .signInViaUsername(self.model.username, self.model.password, self.model.isPersist)
            .then(function () {
                self.router.navigate([self.returnUrl]);
                self.loading = false;
            })
            .catch(function(reason) {
                self.loading = false;
                self.errorMessage = 'Login or password was incorrect.';
            });
    }
}
