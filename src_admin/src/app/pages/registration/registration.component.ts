import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Variable, Extensions, ILogger, ConsoleLogger } from './../../utils/index';
import { IUserApiService, UserApiService } from './../../services/serverApi/index';
import { IAuthorizationService, AuthorizationService } from './../../services/index';
import { IRegistrationModelValidationService, RegistrationModelValidationService } from './../../services/index';
import { RegistrationModelEntity } from './../../entities/index';
import { UserProfileConstants } from './../userProfile/index';
@Component({
    selector: 'registration',
    styleUrls: ['./registration.scss'],
    templateUrl: 'registration.html'
})
export class RegistrationComponent implements OnInit, OnDestroy {
    private _parameterSubscription: any; // type should be Subscription;
    private _afterRegistrationRedirectUrl: string = '/#/pages/home';
    private _cancelRegistrationRedirectUrl: string = '/#/login';
    protected isUsernameValid: boolean = false;
    protected invitationCodeFromUrl: string;
    protected invitationCode: string;
    protected model: RegistrationModelEntity;
    protected registrationPromise: Promise<void>;
    protected checkingUsernamePromise: Promise<boolean> = null;
    protected errorMessage: string;
    protected useValidation: boolean;
    /// injected dependencies
    protected extensions = Extensions;
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    protected authorizationManager: IAuthorizationService;
    protected validationService: IRegistrationModelValidationService;
    /// ctor
    constructor(
        protected route: ActivatedRoute,
        protected router: Router,
        logger: ConsoleLogger,
        userApiService: UserApiService,
        authorizationManager: AuthorizationService,
        registrationModelValidationService: RegistrationModelValidationService) {
        this.userApiService = userApiService;
        this.authorizationManager = authorizationManager;
        this.validationService = registrationModelValidationService;
        this.logger = logger;
        this.logger.logDebug('RegistrationComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        const self = this;
        self.useValidation = false;
        self.model = new RegistrationModelEntity();
        self.model.avatarUrl = UserProfileConstants.userImageDefault
        self.authorizationManager.signOut();
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
    protected register(): Promise<void> {
        if (this.validationService.isValid(this.model) && this.isUsernameValid) {
            this.useValidation = false;
            const self = this;
            self.registrationPromise = self.userApiService
                .register(self.model, self.invitationCode)
                .then(function () {
                    self.router.navigate([self._afterRegistrationRedirectUrl]);
                    self.registrationPromise = null;
                })
                .catch(function (reason) {
                    self.registrationPromise = null;
                    self.errorMessage = 'Registration failed!';
                });
            return self.registrationPromise;
        } else {
            this.useValidation = true;
            return Promise.resolve();
        }
    }
    protected cancelRegistration(): Promise<boolean> {
        return this.router.navigate([this._cancelRegistrationRedirectUrl]);
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
    protected isRegistrationDisabled(): boolean {
        return this.isFormProcessing() ||
            this.isUsernameCheckProcessing();
    }
    protected isFormProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.registrationPromise);
    }
    protected isValidationActive(): boolean {
        return this.useValidation;
    }
    protected isUsernameEmpty(): boolean {
        return Variable.isNullOrUndefined(this.model.username);
    }
    protected isUsernameCheckProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.checkingUsernamePromise);
    }
    /// helpers
    protected msTimeout: number = 1500;
    protected syncKey: string = null;
    protected usernameChanged(newValue: string): void {
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
