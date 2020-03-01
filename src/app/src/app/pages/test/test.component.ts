import { Component, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
@Component({
    selector: 'test',
    styleUrls: ['./test.scss'],
    templateUrl: './test.html',
})
export class Test {

    @ViewChild('testModal1')
    protected testModal1: ModalComponent;
    @ViewChild('testModal2')
    protected testModal2: ModalComponent;
    /// service fields
    private _imageMode: string = 'Default';
    /// data fields
    private _defaultImageUrl: string = 'https://www.b1g1.com/assets/admin/images/no_image_user.png';
    private _testImageUrl: string;
    private _currentImageUrl: string;
    /// ctor
    constructor() { }
    /// methods
    protected getImageUrl(): any {
        if (this._imageMode === 'Default') {
            this._currentImageUrl = this._defaultImageUrl;
        } else {
            this._currentImageUrl = this._testImageUrl;
        }
        return this._currentImageUrl;
    }
    defineTestImage(): void {
        this._testImageUrl = 'https://lh3.googleusercontent.com/X-e8ol99z-1kGJ_EmqqfN-nqDvNMKiTEUlIWtGk-L4NxkVX3-8qThkVJKaUgF5iJFA=w300';
    }
    nullifyTestImage(): void {
        this._testImageUrl = null;
    }
    setModeDefault(): void {
        this._imageMode = 'Default';
    }
    setModeTest(): void {
        this._imageMode = 'Test';
    }
}
