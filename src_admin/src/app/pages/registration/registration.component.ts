import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Extensions } from './../../utils/index';
import { IUserApiService, UserApiService } from './../../services/serverApi/index';
@Component({
    selector: 'registration',
    styleUrls: ['./registration.scss'],
    templateUrl: 'registration.html'
})
export class RegistrationComponent implements OnInit, OnDestroy {
    private _parameterSubscription: any; // type should be Subscription;
    private _afterRegistrationRedirectUrl: string = '';
    protected isUsernameValid: boolean = false;
    protected confirmPassword: string = null;
    protected invitationCodeFromUrl: string;
    protected invitationCode: string;
    protected model: any = {
        username: null,
        email: null,
        password: null,
        firstName: null,
        secondName: null,
        phoneNumber: null,
        avatarUrl: null
    };
    protected loading = false;
    protected checkingUsernamePromise: Promise<boolean> = null;
    protected errorMessage: string;
    /// injected dependencies
    protected userApiService: IUserApiService;
    /// ctor
    constructor(
        private route: ActivatedRoute,
        private router: Router,
        userApiService: UserApiService) {
        this.userApiService = userApiService;
    }
    /// methods
    ngOnInit(): void {
        const self = this;
        self._parameterSubscription = self.route.params
            .subscribe(params => {
                self.invitationCodeFromUrl = params['invitationCode'];
                self.invitationCode = self.invitationCodeFromUrl;
            });
    }
    ngOnDestroy(): void {
        if (this._parameterSubscription &&
            this._parameterSubscription.unsubscribe) {
            this._parameterSubscription.unsubscribe();
        }
    }
    protected register(): void {
        const self = this;
        self.loading = true;
        self.userApiService
            .register(self.model, self.invitationCode)
            .then(function () {
                self.router.navigate([self._afterRegistrationRedirectUrl]);
                self.loading = false;
            })
            .catch(function(reason) {
                self.loading = false;
                self.errorMessage = 'Registration failed!';
            });
    }
    protected isUsernameAvailable(value): Promise<boolean> {
        const self = this;
        self.checkingUsernamePromise = self.userApiService
            .isUsernameValid(value)
            .then(function (response: boolean): boolean {
                self.checkingUsernamePromise = null;
                self.isUsernameValid = response;
                return response;
            })
            .catch(function(reason) {
                self.checkingUsernamePromise = null;
            });
        return self.checkingUsernamePromise;
    }
    /// predicates
    protected isRegistrationAvailable(): boolean {
        return this.isModelValid() && !this.loading;
    }
    protected isModelValid(): boolean {
        return this.isUsernameValid &&
            this.isValidEmail(this.model.email) &&
            this.isUsernameValid && this.checkingUsernamePromise === null &&
            this.model.password === this.confirmPassword;
    }
    protected isValidEmail(value: string): boolean {
        return Extensions.regExp.email.test(value)
    }
    /// helpers
    protected msTimeout: number = 3000;
    protected syncKey: string = null;
    usernameChanged(newValue: string): void {
        if (this.model.username !== newValue) {
            const currentSyncKey: string = Extensions.generateGuid();
            const self = this;
            self.model.username = newValue;
            self.isUsernameValid = false;
            self.syncKey = currentSyncKey;
            setTimeout(
                () => {
                    if (self.syncKey === currentSyncKey) {
                        self.syncKey = null;
                        self.isUsernameAvailable(newValue)
                            .then(function(result: boolean) {
                                self.isUsernameValid = result;
                            });
                    }
                },
                self.msTimeout);
        }
    }
}
