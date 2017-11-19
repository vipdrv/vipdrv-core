import { CanActivate } from '@angular/router';
export interface IAuthorizationService extends CanActivate {
    /// url to redirect after authorization
    postAuthRedirectUrl: string;

    // #warning: use user here, not any
    lastUser: any;
    user: Promise<any>;

    signInViaUsername(login: string, password: string, isPersist: boolean): Promise<any>;
    signOut(): Promise<any>;
    handleAuthorizationCallback(): Promise<void>;
}