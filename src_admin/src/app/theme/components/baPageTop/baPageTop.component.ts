import {Component} from '@angular/core';

import {GlobalState} from '../../../global.state';

import { IAuthorizationManager, AuthorizationManager, Variable } from './../../../utils/index';

@Component({
  selector: 'ba-page-top',
  templateUrl: './baPageTop.html',
  styleUrls: ['./baPageTop.scss']
})
export class BaPageTop {

    public isScrolled: boolean = false;
    public isMenuCollapsed: boolean = false;

    protected authorizationManager: IAuthorizationManager;

    constructor(private _state: GlobalState, authorizationManager: AuthorizationManager) {
        this.authorizationManager = authorizationManager;
        this._state.subscribe('menu.isCollapsed', (isCollapsed) => {
            this.isMenuCollapsed = isCollapsed;
        });
    }

    public toggleMenu() {
        this.isMenuCollapsed = !this.isMenuCollapsed;
        this._state.notifyDataChanged('menu.isCollapsed', this.isMenuCollapsed);
        return false;
    }

    public scrolledChanged(isScrolled) {
        this.isScrolled = isScrolled;
    }

    isUserInfoDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.authorizationManager.lastUser);
    }
    userAvatarUrl(): string {
        // TODO: replace with real user avatar
        let result: string = 'https://www.b1g1.com/assets/admin/images/no_image_user.png';
        return result;
    }
}
