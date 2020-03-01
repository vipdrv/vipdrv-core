import { Injectable } from '@angular/core';
/// is used to store information about all application promises
@Injectable()
export class PromiseService {
    private _applicationPromises: any;
    get applicationPromises(): any {
        return this._applicationPromises;
    }
    set applicationPromises(value: any) {
        throw new Error('This operation is not allowed!');
    }
    /// ctor
    constructor() {
        this.initializePromisesStore();
    }
    /// helpers
    private initializePromisesStore(): void {
        this._applicationPromises = {
            sites: {
                getAll: {
                    promise: null
                },
                get: {
                    promise: null,
                    entityId: null
                },
                addOrUpdate: {
                    promise: null,
                    entityId: null
                },
                delete: {
                    promise: null,
                    entityId: null
                },
                patch: {
                    contactsPromise: null
                }
            },
            experts: {
                getAll: {
                    promise: null
                },
                get: {
                    promise: null,
                    entityId: null
                },
                addOrUpdate: {
                    promise: null,
                    entityId: null
                },
                delete: {
                    promise: null,
                    entityId: null
                }
            },
        };
    }
}