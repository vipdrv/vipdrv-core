import { Component, ViewChild, OnInit } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../../utils/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
import { IUserApiService, UserApiService } from './../../../services/index';
@Component({
    selector: 'user-avatar-update',
    styleUrls: ['./avatarUpdate.scss'],
    templateUrl: './avatarUpdate.html',
})
export class AvatarUpdateComponent implements OnInit {
    /// service fields
    protected userLoadingPromise: Promise<void>;
    /// fields
    protected userId: number;
    /// injected dependencies
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    /// ctor
    constructor(logger: ConsoleLogger, userApiService: UserApiService) {
        this.logger = logger;
        this.userApiService = userApiService;
        this.logger.logDebug('AvatarUpdateComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.avatarWidth = 300;
        this.avatarHeight = 300;
        this.initializeAvatarCropper();
    }


    /// avatar
    @ViewChild('cropper', undefined)

    private _currentAvatarUrl: string = 'https://www.b1g1.com/assets/admin/images/no_image_user.png';

    protected defaultAvatarUrl: string = 'https://www.b1g1.com/assets/admin/images/no_image_user.png';

    protected cropper: ImageCropperComponent;
    protected avatarWidth: number;
    protected avatarHeight: number;
    protected avatarCropperData: any;
    protected avatarCropperSettings: CropperSettings;
    protected stubAvatarUrl: string;
    private _avatarMode: string = 'View';
    isAvatarInViewMode(): boolean {
        return this._avatarMode === 'View';
    }
    isAvatarInEditMode(): boolean {
        return this._avatarMode === 'Edit';
    }
    isAvatarInEditUrlMode(): boolean {
        return this._avatarMode === 'EditUrl';
    }
    protected getAvatar(): any {
        if (this.isAvatarInViewMode()) {
            return this.defaultAvatarUrl; //this._currentAvatarUrl;
        } else if (this.isAvatarInEditMode() && this.avatarCropperData && this.avatarCropperData.image) {
            return Variable.isNotNullOrUndefined(this.avatarCropperData.image) ?
                this.avatarCropperData.image : this._currentAvatarUrl;
        } else if (this.isAvatarInEditUrlMode()) {
            return Variable.isNotNullOrUndefined(this.stubAvatarUrl) ? this.stubAvatarUrl : this._currentAvatarUrl;
        } else {
            return this.defaultAvatarUrl;
        }
    }
    protected editAvatar(): void {
        this.avatarCropperData = {};
        this._avatarMode = 'Edit';
    }
    protected editAvatarCommit(): void {
        const self = this;
        this._currentAvatarUrl = self.avatarCropperData.image; // imageUrl;
        this.editAvatarCancel();
    }
    protected editAvatarCancel(): void {
        this.avatarCropperData = null;
        this._avatarMode = 'View';
    }
    protected avatarBrowseFileChangeListener($event) {
        const image: any = new Image();
        const file: File = $event.target.files[0];
        const fileReader: FileReader = new FileReader();
        const self = this;
        fileReader.onloadend = function (loadEvent: any): void {
            image.src = loadEvent.target.result;
            self.cropper.setImage(image);

        };
        fileReader.readAsDataURL(file);
    }
    protected editAvatarUrl(): void {
        this._avatarMode = 'EditUrl';
    }
    protected editAvatarUrlCommit(): void {
        this._currentAvatarUrl = this.stubAvatarUrl;
        this.editAvatarUrlCancel();
    }
    protected editAvatarUrlCancel(): void {
        this.stubAvatarUrl = null;
        this._avatarMode = 'View';
    }
    private _changeAvatarPromise: Promise<void>;
    saveAvatar(): Promise<void> {
        const self = this;
        self._changeAvatarPromise = self.userApiService
            .patchAvatar(
                self.authorizationService.lastUser.userId,
                self._currentAvatarUrl)
            .then(function() {

            })
            .then(
                () => {
                    self._changeAvatarPromise = null;
                },
                () => {
                    self._changeAvatarPromise = null;
                }
            );
        return self._changeAvatarPromise;
    }
    resetAvatar(): void {
        this._currentAvatarUrl = this.defaultAvatarUrl;
    }
    private initializeAvatarCropper(): void {
        this.avatarCropperSettings = new CropperSettings();
        this.avatarCropperSettings.rounded = true;
        this.avatarCropperSettings.noFileInput = true;
        this.avatarCropperSettings.minWithRelativeToResolution = true;
        this.avatarCropperSettings.minWidth = this.avatarWidth;
        this.avatarCropperSettings.minHeight = this.avatarHeight;
        this.avatarCropperSettings.width = this.avatarWidth;
        this.avatarCropperSettings.height = this.avatarHeight;
        this.avatarCropperSettings.croppedWidth = this.avatarWidth;
        this.avatarCropperSettings.croppedHeight = this.avatarHeight;
        this.avatarCropperSettings.canvasWidth = this.avatarWidth - 40;
        this.avatarCropperSettings.canvasHeight = this.avatarHeight - 40;
        this.avatarCropperData = {};
        this.logger.logTrase('UserProfileComponent: Avatar cropper has been initialized.');
    }
    /// predicates
    isUserIdDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.userId);
    }
    /// helpers
}
