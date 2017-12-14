import { CanActivate } from '@angular/router';
import { UserEntity } from './../../entities/index';
export interface IAuthorizationService extends CanActivate {
    /// url to redirect after authorization
    postAuthRedirectUrl: string;
    // returns current user id or null
    currentUserId: number;
    // returns currecnt user permissions
    currentUserPermissions: Array<string>;
    // returns current user or null
    currentUser: UserEntity;
    // returns auth token (with prefix) that can be just used in Authorization header or null (if not token)
    getAuthorizationToken(): string;
    // is used to retrieve info about current user
    actualizeCurrentUserProfile(): Promise<void>;
    signInViaUsername(login: string, password: string, isPersist: boolean): Promise<any>;
    signOut(): Promise<any>;
    handleUnauthorizedResponse(): Promise<void>;
    isUnauthorizedError(reason: any): boolean;
}