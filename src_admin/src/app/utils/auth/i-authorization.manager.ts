export interface IAuthorizationManager {
    /// url to redirect after authorization
    postAuthRedirectUrl: string;

    // #warning: use user here, not any
    user: Promise<any>;
    lastUser: any;

    signIn(args?: any): Promise<any>;
    signOut(args?: any): Promise<any>;
    handleAuthorizationCallback(): Promise<void>;
}