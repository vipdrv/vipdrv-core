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
    @Input() imageAlt: string;
    @Input() imageWidth: number = 300;
    @Input() imageHeight: number = 300;
    @Input() isRounded: boolean = false;
    /// ctor
    constructor() { }
    /// methods
    ngOnInit(): void {

    }
    ngOnChanges(changes: SimpleChanges) {

    }
}