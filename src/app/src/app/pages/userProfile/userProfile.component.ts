import { Component, OnInit } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../utils/index';
import { UserEntity } from './../../entities/index';
import { IUserApiService, UserApiService, IAuthorizationService, AuthorizationService } from './../../services/index';
@Component({
    selector: 'user-profile',
    styleUrls: ['./userProfile.scss'],
    templateUrl: './userProfile.html',
})
export class UserProfileComponent implements OnInit {
    /// service fields
    protected userLoadingPromise: Promise<void>;
    /// fields
    protected userId: number;
    protected user: UserEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    protected authorizationService: IAuthorizationService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        userApiService: UserApiService,
        authorizationService: AuthorizationService) {
        this.logger = logger;
        this.userApiService = userApiService;
        this.authorizationService = authorizationService;
        this.logger.logDebug('UserProfileComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        if (Variable.isNotNullOrUndefined(this.authorizationService.currentUserId)) {
            this.userId = this.authorizationService.currentUserId;
            if (this.isUserIdDefined()) {
                this.loadUser(this.userId);
            }
        }
    }
    onUpdateAvatarUrl(newAvatarUrl: string): void {
        this.user.avatarUrl = newAvatarUrl;
        this.notifyObserversOnChangeProfile();
    }
    onUpdatePassword(): void {
        // do not need to notify about this event
    }
    onUpdatePersonalInfo(newPersonalInfo: any): void {
        this.user.firstName = newPersonalInfo.firstName;
        this.user.secondName = newPersonalInfo.secondName;
        this.user.email = newPersonalInfo.email;
        this.user.phoneNumber = newPersonalInfo.phoneNumber;
        this.notifyObserversOnChangeProfile();
    }
    notifyObserversOnChangeProfile(): Promise<void> {
        return this.authorizationService.actualizeCurrentUserProfile();
    }
    /// predicates
    isUserIdDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.userId);
    }
    isUserDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.user);
    }
    /// helpers
    protected loadUser(userId: number): Promise<void> {
        const self = this;
        self.userLoadingPromise = self.userApiService
            .get(userId)
            .then(function(response: UserEntity): void {
                self.user = response;
            })
            .then(
                () => {
                    self.userLoadingPromise = null;
                },
                () => {
                    self.userLoadingPromise = null;
                },
            );
        return self.userLoadingPromise;
    }
}
