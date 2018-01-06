import { Component, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { OnInit, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { Variable } from './../../../utils/index';
@Component({
    selector: 'image-view',
    styleUrls: ['./imageView.scss'],
    templateUrl: './imageView.html',
})
export class ImageViewComponent implements OnInit, OnChanges {
    @Input() imageUrl: string;
    @Input() imageAlt: string = '';
    @Input() imageWidth: number = null;
    @Input() imageHeight: number = null;
    @Input() isRounded: boolean = false;
    @Input() externalImageClass: string = '';
    @Input() useHeightAsMainProperty: boolean = true;
    /// ctor
    constructor() { }
    /// methods
    ngOnInit(): void {

    }
    ngOnChanges(changes: SimpleChanges) {

    }
    protected getContainerStyle() {
        return {
            'overflow': 'hidden',
            'height': `${this.imageHeight}px`,
            'width': `${this.imageWidth}px`,
        };
    }
    protected getImageStyle() {
        return this.useHeightAsMainProperty ?
            {
                'height': '100%',
            } : {
                'width': '100%',
            };
    }
    getImageClass(): any {
        return {
            'rounded-circle': this.isRounded
        };
    }
}